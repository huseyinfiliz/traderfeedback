<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackRejected;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackNeedsApprovalBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class RejectFeedbackController implements RequestHandlerInterface
{
    public function __construct(
        protected NotificationSyncer $notifications,
        protected LoggerInterface $log,
        protected Dispatcher $events
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $id = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        $actor->assertCan('huseyinfiliz-traderfeedback.moderate');

        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);

        // Notify feedback author about rejection BEFORE deleting
        if ($feedback->fromUser) {
            try {
                $blueprint = new FeedbackRejectedBlueprint($feedback);
                $this->notifications->sync($blueprint, [$feedback->fromUser]);
            } catch (\Exception $e) {
                $this->log->error('Failed to send rejection notification', [
                    'feedback_id' => $feedback->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        // Clear pending approval notifications for moderators
        try {
            $this->notifications->sync(new FeedbackNeedsApprovalBlueprint($feedback), []);
        } catch (\Exception $e) {
            // Ignore if notification cannot be synced
        }

        // Soft delete the feedback
        $feedback->is_approved = false;
        $feedback->delete();

        // Update stats
        StatsService::updateUserStats($feedback->to_user_id);

        // Dispatch event for audit logs
        $this->events->dispatch(new FeedbackRejected($feedback, $actor));

        return new JsonResponse([
            'data' => FeedbackSerializer::feedback($feedback, $actor),
        ]);
    }
}
