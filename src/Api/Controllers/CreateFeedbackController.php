<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use Flarum\Locale\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;
use Illuminate\Contracts\Events\Dispatcher;
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
        protected TranslatorInterface $translator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);

        // Authorization: must be registered and hold the "give" permission.
        $actor->assertRegistered();
        $actor->assertCan('huseyinfiliz-traderfeedback.give');

        // Check account age requirement (minDays)
        $minDays = (int) $this->settings->get('huseyinfiliz.traderfeedback.minDays', 0);
        if ($minDays > 0 && $actor->joined_at) {
            $daysSinceJoined = $actor->joined_at->diffInDays(Carbon::now(), true);
            if ($daysSinceJoined < $minDays) {
                throw new ValidationException([
                    'user' => $this->translator->trans(
                        'huseyinfiliz-traderfeedback.api.validation.min_days',
                        ['days' => $minDays]
                    ),
                ]);
            }
        }

        // Check post count requirement (minPosts)
        $minPosts = (int) $this->settings->get('huseyinfiliz.traderfeedback.minPosts', 0);
        if ($minPosts > 0 && (int) $actor->comment_count < $minPosts) {
            throw new ValidationException([
                'user' => $this->translator->trans(
                    'huseyinfiliz-traderfeedback.api.validation.min_posts',
                    ['posts' => $minPosts]
                ),
            ]);
        }

        $body = $request->getParsedBody();
        $data = Arr::get($body, 'data.attributes') ?: Arr::get($body, 'data') ?: $body;
        if (!is_array($data)) {
            $data = [];
        }

        // Rate Limit Check: Max 1 feedback per minute
        $recentFeedback = Feedback::where('from_user_id', $actor->id)
            ->where('created_at', '>', Carbon::now()->subMinute())
            ->exists();

        if ($recentFeedback) {
            throw new ValidationException([
                'rate_limit' => $this->translator->trans(
                    'huseyinfiliz-traderfeedback.api.validation.rate_limit_feedback',
                    ['seconds' => 60]
                ),
            ]);
        }

        // Parse discussion ID from URL or input if provided BEFORE validator runs
        $rawDiscussionId = Arr::get($data, 'discussion_id');
        $discussionId = null;
        if ($rawDiscussionId) {
            if (is_string($rawDiscussionId) && preg_match('/\/d\/(\d+)/', $rawDiscussionId, $matches)) {
                $discussionId = (int) $matches[1];
                $data['discussion_id'] = $discussionId;
            } elseif (is_numeric($rawDiscussionId)) {
                $discussionId = (int) $rawDiscussionId;
                $data['discussion_id'] = $discussionId;
            }
        }

        // Check if discussion is required
        $requireDiscussion = (bool) $this->settings->get('huseyinfiliz.traderfeedback.requireDiscussion', false);
        if ($requireDiscussion && !$discussionId) {
            throw new ValidationException([
                'discussion_id' => 'Discussion URL or ID is required for feedback.',
            ]);
        }

        // Check allow negative setting
        $allowNegative = $this->settings->get('huseyinfiliz.traderfeedback.allowNegative');
        if (($allowNegative === false || $allowNegative === '0' || $allowNegative === 0) && Arr::get($data, 'type') === Feedback::TYPE_NEGATIVE) {
            throw new ValidationException([
                'type' => 'Negative feedback is not allowed.',
            ]);
        }

        // XSS Protection: Strip HTML tags from comment before validation
        $rawComment = (string) Arr::get($data, 'comment', '');
        $sanitizedComment = strip_tags($rawComment);
        $data['comment'] = $sanitizedComment;

        // Validate the request data
        $this->validator->assertValid($data);

        // Check if user is trying to give feedback to themselves
        $toUserId = (int) Arr::get($data, 'to_user_id');
        if ($actor->id === $toUserId) {
            throw new ValidationException([
                'to_user_id' => 'You cannot give feedback to yourself.',
            ]);
        }

        // Check one per discussion rule
        $onePerDiscussion = (bool) $this->settings->get('huseyinfiliz.traderfeedback.onePerDiscussion', true);
        if ($onePerDiscussion && $discussionId) {
            $existingFeedback = Feedback::where('from_user_id', $actor->id)
                ->where('to_user_id', $toUserId)
                ->where('discussion_id', $discussionId)
                ->exists();

            if ($existingFeedback) {
                throw new ValidationException([
                    'discussion_id' => 'You have already given feedback for this user in this discussion.',
                ]);
            }
        }

        // Validate discussion exists if provided
        if ($discussionId) {
            $discussionExists = Discussion::find($discussionId);
            if (!$discussionExists) {
                throw new ValidationException([
                    'discussion_id' => 'The specified discussion does not exist.',
                ]);
            }

            // Check if discussion is deleted/hidden
            if ($discussionExists->hidden_at !== null) {
                throw new ValidationException([
                    'discussion_id' => 'The specified discussion is not available.',
                ]);
            }
        }

        // Create the feedback
        $feedback = new Feedback();
        $feedback->from_user_id = $actor->id;
        $feedback->to_user_id = $toUserId;
        $feedback->type = Arr::get($data, 'type');
        $feedback->comment = $sanitizedComment;
        $feedback->role = Arr::get($data, 'role');
        $feedback->discussion_id = $discussionId;
        $feedback->is_approved = !(bool) $this->settings->get('huseyinfiliz.traderfeedback.requireApproval', false);

        $feedback->save();

        // Load relationships
        $feedback->load(['fromUser', 'toUser', 'discussion']);

        // Fire the event - listener will handle stats update and notifications
        $this->events->dispatch(new FeedbackCreated($feedback, $actor));

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
        ], 201);
    }
}
