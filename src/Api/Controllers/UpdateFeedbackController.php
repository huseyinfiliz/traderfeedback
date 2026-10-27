<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Services\FeedbackGuard;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;
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
        protected Dispatcher $events,
        protected FeedbackGuard $guard,
        protected ConnectionInterface $db
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

        $feedback = $this->db->transaction(function () use ($id, $actor, $data) {
            $feedback = Feedback::whereKey($id)->lockForUpdate()->firstOrFail();
            $actor->assertCan('edit', $feedback);

            $changes = Arr::only($data, ['type', 'comment', 'role']);
            if (isset($changes['comment']) && is_string($changes['comment'])) {
                $changes['comment'] = trim(strip_tags($changes['comment']));
            }
            $this->validator->validateMissingKeys(false)->assertValid($changes);
            if (isset($changes['type'])) {
                $this->guard->assertType($changes['type']);
            }

            $feedback->fill($changes);
            if ($feedback->isDirty(['type', 'comment', 'role'])) {
                if (!$actor->hasPermission('huseyinfiliz-traderfeedback.moderate')
                    && (bool) $this->settings->get('huseyinfiliz.traderfeedback.requireApproval', false)) {
                    $feedback->is_approved = false;
                    $feedback->approved_by_id = null;
                }
                $feedback->save();
            }

            return $feedback;
        }, 3);

        $feedback->load(['fromUser', 'toUser', 'discussion' => fn ($query) => $query->whereVisibleTo($actor)]);
        if ($feedback->wasChanged()) {
            $this->events->dispatch(new FeedbackUpdated($feedback, $actor));
        }

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
            'data'     => FeedbackSerializer::feedback($feedback, $actor),
            'included' => $included,
        ]);
    }
}
