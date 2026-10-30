<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\Notification\Notification;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackNeedsApprovalBlueprint;
use HuseyinFiliz\TraderFeedback\Services\ModeratorNotificationSyncer;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class FeedbackUpdatedListener
{
    public function __construct(
        protected NotificationSyncer $notifications,
        protected StatsService $stats,
        protected ModeratorNotificationSyncer $moderatorNotifications
    ) {
    }

    public function handle(FeedbackUpdated $event)
    {
        $feedback = $event->feedback;
        $actor = $event->actor;

        $this->stats->updateUserStats($feedback->to_user_id);

        $feedback->loadMissing(['fromUser', 'toUser']);

        if (!$feedback->is_approved) {
            // Blueprint matching includes data, which has changed along with the feedback.
            Notification::whereSubject($feedback)->whereIn('type', ['newFeedback', 'feedbackApproved', 'feedbackNeedsApproval'])
                ->update(['is_deleted' => true]);
            $this->moderatorNotifications->syncModerators(new FeedbackNeedsApprovalBlueprint($feedback), $actor->id);

            return;
        }

        if ($feedback->fromUser && $feedback->fromUser->id !== $actor->id) {
            $this->notifications->sync(
                new FeedbackApprovedBlueprint($feedback),
                [$feedback->fromUser]
            );
        }
    }
}
