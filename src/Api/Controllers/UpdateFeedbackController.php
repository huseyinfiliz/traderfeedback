<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;

class UpdateFeedbackController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackSerializer::class;

    /**
     * @var FeedbackValidator
     */
    protected $validator;

    /**
     * @param FeedbackValidator $validator
     */
    public function __construct(FeedbackValidator $validator)
    {
        $this->validator = $validator;
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
        
        // Validate the request data
        $this->validator->assertValid($data);
        
        // Update the feedback
        if (isset($data['type'])) {
            $feedback->type = $data['type'];
        }
        
        if (isset($data['comment'])) {
            $feedback->comment = $data['comment'];
        }
        
        if (isset($data['role'])) {
            $feedback->role = $data['role'];
        }
        
        if (isset($data['transaction_id'])) {
            $feedback->transaction_id = $data['transaction_id'];
        }
        
        $feedback->save();
        
        return $feedback;
    }
}