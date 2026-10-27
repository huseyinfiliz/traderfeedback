<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
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

class CreateFeedbackController implements RequestHandlerInterface
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
        $actor->assertRegistered();
        $actor->assertCan('huseyinfiliz-traderfeedback.give');
        $this->guard->assertEligible($actor);

        $body = $request->getParsedBody();
        $data = Arr::get($body, 'data.attributes') ?? Arr::get($body, 'data') ?? $body;
        $data = is_array($data) ? $data : [];
        if (isset($data['comment']) && is_string($data['comment'])) {
            $data['comment'] = trim(strip_tags($data['comment']));
        }
        $data['discussion_id'] = $this->guard->discussionId($data['discussion_id'] ?? null);
        $this->validator->validateMissingKeys()->assertValid($data);
        $this->guard->assertType($data['type']);
        $this->guard->assertDiscussion($actor, $data['discussion_id']);

        $feedback = $this->db->transaction(function () use ($actor, $data) {
            // Serialize submissions by actor, including the first submission with no existing feedback row.
            User::whereKey($actor->id)->select('id')->lockForUpdate()->firstOrFail();
            $this->guard->assertCanCreate($actor, (int) $data['to_user_id'], $data['discussion_id']);

            $feedback = new Feedback();
            $feedback->fill(Arr::only($data, ['to_user_id', 'type', 'role', 'comment', 'discussion_id']));
            $feedback->from_user_id = $actor->id;
            $feedback->is_approved = !(bool) $this->settings->get('huseyinfiliz.traderfeedback.requireApproval', false);
            $feedback->save();

            return $feedback;
        }, 3);

        $feedback->load(['fromUser', 'toUser', 'discussion' => fn ($query) => $query->whereVisibleTo($actor)]);
        $this->events->dispatch(new FeedbackCreated($feedback, $actor));

        return new JsonResponse([
            'data'     => FeedbackSerializer::feedback($feedback, $actor),
            'included' => array_values(array_filter([
                $feedback->fromUser ? FeedbackSerializer::user($feedback->fromUser) : null,
                $feedback->toUser ? FeedbackSerializer::user($feedback->toUser) : null,
                $feedback->discussion ? FeedbackSerializer::discussion($feedback->discussion) : null,
            ])),
        ], 201);
    }
}
