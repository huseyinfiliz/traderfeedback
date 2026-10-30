<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackNeedsApprovalBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use HuseyinFiliz\TraderFeedback\Services\ModeratorNotificationSyncer;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class FeedbackCreatedListener
{
    public function __construct(
        protected NotificationSyncer $notifications,
        protected StatsService $stats,
        protected ModeratorNotificationSyncer $moderatorNotifications
    ) {
    }

    public function handle(FeedbackCreated $event)
    {
        $feedback = $event->feedback;

        // Load relationships
        if (!$feedback->relationLoaded('toUser')) {
            $feedback->load('toUser');
        }
        if (!$feedback->relationLoaded('fromUser')) {
            $feedback->load('fromUser');
        }

        if ($feedback->is_approved) {
            // Stats update
            $this->updateUserStats($feedback->to_user_id);

            // Notify feedback recipient
            if ($feedback->toUser && $feedback->toUser->id !== $feedback->from_user_id) {
                $this->notifications->sync(
                    new NewFeedbackBlueprint($feedback),
                    [$feedback->toUser]
                );
            }
        } else {
            // Feedback requires approval -> notify moderators
            $this->moderatorNotifications->syncModerators(
                new FeedbackNeedsApprovalBlueprint($feedback),
                $feedback->from_user_id
            );
        }
    }

    protected function updateUserStats($userId)
    {
        $this->stats->updateUserStats((int) $userId);
    }
}
