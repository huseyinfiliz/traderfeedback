<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\Notification\Notification;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackNeedsApprovalBlueprint;
use HuseyinFiliz\TraderFeedback\Services\ModeratorFinder;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class FeedbackUpdatedListener
{
    public function __construct(
        protected NotificationSyncer $notifications,
        protected StatsService $stats,
        protected ModeratorFinder $moderatorFinder
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
            $moderators = $this->moderatorFinder->getModerators($actor->id);
            $this->notifications->sync(new FeedbackNeedsApprovalBlueprint($feedback), $moderators->all());

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
