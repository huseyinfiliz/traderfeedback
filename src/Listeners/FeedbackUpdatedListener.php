<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class FeedbackUpdatedListener
{
    public function __construct(protected NotificationSyncer $notifications)
    {
    }

    public function handle(FeedbackUpdated $event)
    {
        $feedback = $event->feedback;
        $actor = $event->actor;

        if (!$feedback->is_approved) {
            return;
        }

        // İlişkileri yükle
        $feedback->loadMissing(['fromUser', 'toUser']);

        // Stats güncelle
        $this->updateUserStats($feedback->to_user_id);

        // Bildirim gönder
        if ($feedback->fromUser && $feedback->fromUser->id !== $actor->id) {
            $this->notifications->sync(
                new FeedbackApprovedBlueprint($feedback),
                [$feedback->fromUser]
            );
        }
    }

    protected function updateUserStats($userId)
    {
        StatsService::updateUserStats((int) $userId);
    }
}
