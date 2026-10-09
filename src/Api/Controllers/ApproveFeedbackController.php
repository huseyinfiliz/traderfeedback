<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Events\FeedbackApproved;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackNeedsApprovalBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class ApproveFeedbackController implements RequestHandlerInterface
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

        $wasApproved = (bool) $feedback->is_approved;

        // Approve feedback
        $feedback->is_approved = true;
        $feedback->approved_by_id = $actor->id;
        $feedback->save();

        // Update stats
        StatsService::updateUserStats($feedback->to_user_id);

        // Send notifications if newly approved
        if (!$wasApproved) {
            try {
                // 1. Notify feedback author about approval
                if ($feedback->fromUser) {
                    $approvalBlueprint = new FeedbackApprovedBlueprint($feedback);
                    $this->notifications->sync($approvalBlueprint, [$feedback->fromUser]);
                }

                // 2. Notify feedback recipient about new feedback
                if ($feedback->toUser) {
                    $newFeedbackBlueprint = new NewFeedbackBlueprint($feedback);
                    $this->notifications->sync($newFeedbackBlueprint, [$feedback->toUser]);
                }

                // 3. Clear pending approval notifications for moderators
                $this->notifications->sync(new FeedbackNeedsApprovalBlueprint($feedback), []);
            } catch (\Exception $e) {
                $this->log->error('Failed to send approval notifications', [
                    'feedback_id' => $feedback->id,
                    'error'       => $e->getMessage(),
                ]);
            }

            // Dispatch event for audit logs
            $this->events->dispatch(new FeedbackApproved($feedback, $actor));
        }

        return new JsonResponse([
            'data' => FeedbackSerializer::feedback($feedback, $actor),
        ]);
    }
}
