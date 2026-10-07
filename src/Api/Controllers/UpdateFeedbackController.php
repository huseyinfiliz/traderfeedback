<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UpdateFeedbackController implements RequestHandlerInterface
{
    public function __construct(
        protected FeedbackValidator $validator,
        protected SettingsRepositoryInterface $settings,
        protected Dispatcher $events
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $body = $request->getParsedBody();
        $data = Arr::get($body, 'data.attributes') ?: Arr::get($body, 'data') ?: $body;
        if (!is_array($data)) {
            $data = [];
        }

        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $id = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::with(['fromUser', 'toUser', 'discussion'])->findOrFail($id);

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

        $this->events->dispatch(new FeedbackUpdated($feedback, $actor));

        $included = [];
        if ($feedback->fromUser) {
            $included[] = FeedbackSerializer::user($feedback->fromUser);
        }
        if ($feedback->toUser) {
            $included[] = FeedbackSerializer::user($feedback->toUser);
        }
        if ($feedback->discussion) {
            $included[] = FeedbackSerializer::discussion($feedback->discussion);
        }

        return new JsonResponse([
            'data' => FeedbackSerializer::feedback($feedback, $actor),
            'included' => $included,
        ]);
    }
}
