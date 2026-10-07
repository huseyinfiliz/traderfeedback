<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateFeedbackController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['fromUser', 'toUser', 'discussion'];

    /**
     * @var FeedbackValidator
     */
    protected $validator;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @param FeedbackValidator           $validator
     * @param SettingsRepositoryInterface $settings
     */
    public function __construct(FeedbackValidator $validator, SettingsRepositoryInterface $settings)
    {
        $this->validator = $validator;
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);
        $id = Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::findOrFail($id);

        $actor->assertCan('edit', $feedback);

        $validationData = [];

        if (isset($data['type'])) {
            $allowNegative = $this->settings->get('huseyinfiliz.traderfeedback.allowNegative');
            if (($allowNegative === false || $allowNegative === '0' || $allowNegative === 0) && $data['type'] === Feedback::TYPE_NEGATIVE) {
                throw new ValidationException([
                    'type' => 'Negative feedback is not allowed.',
                ]);
            }

            $validationData['type'] = $data['type'];
            $feedback->type = $data['type'];
        }

        if (isset($data['comment'])) {
            $sanitizedComment = strip_tags($data['comment']);
            $validationData['comment'] = $sanitizedComment;
            $feedback->comment = $sanitizedComment;
        }

        if (isset($data['role'])) {
            $validationData['role'] = $data['role'];
            $feedback->role = $data['role'];
        }

        if (!empty($validationData)) {
            $this->validator->assertValid($validationData);
        }

        $feedback->save();

        event(new FeedbackUpdated($feedback, $actor));

        return $feedback;
    }
}
