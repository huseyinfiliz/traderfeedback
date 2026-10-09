<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackNeedsApprovalBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use HuseyinFiliz\TraderFeedback\Services\ModeratorFinder;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class FeedbackCreatedListener
{
    public function __construct(protected NotificationSyncer $notifications)
    {
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
            $moderators = ModeratorFinder::getModerators($feedback->from_user_id);

            if ($moderators->isNotEmpty()) {
                $this->notifications->sync(
                    new FeedbackNeedsApprovalBlueprint($feedback),
                    $moderators->all()
                );
            }
        }
    }

    protected function updateUserStats($userId)
    {
        StatsService::updateUserStats((int) $userId);
    }
}
