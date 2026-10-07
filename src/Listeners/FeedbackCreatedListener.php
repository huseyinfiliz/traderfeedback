<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class FeedbackCreatedListener
{
    protected $notifications;

    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(FeedbackCreated $event)
    {
        $feedback = $event->feedback;

        // İlişkileri yükle
        if (!$feedback->relationLoaded('toUser')) {
            $feedback->load('toUser');
        }

        // SADECE ONAYLI İSE İŞLEM YAP
        if ($feedback->is_approved) {
            // Stats güncelle
            $this->updateUserStats($feedback->to_user_id);

            // Bildirim gönder
            if ($feedback->toUser && $feedback->toUser->id !== $feedback->from_user_id) {
                app('log')->info('Sending newFeedback notification (approved)', [
                    'feedback_id' => $feedback->id,
                    'to_user'     => $feedback->toUser->id,
                    'is_approved' => $feedback->is_approved,
                ]);

                $this->notifications->sync(
                    new NewFeedbackBlueprint($feedback),
                    [$feedback->toUser]
                );
            }
        } else {
            // ONAYLI DEĞİLSE BİLDİRİM GÖNDERME!
            app('log')->info('Feedback not approved, skipping notification', [
                'feedback_id' => $feedback->id,
                'is_approved' => $feedback->is_approved,
            ]);
        }
    }

    protected function updateUserStats($userId)
    {
        StatsService::updateUserStats((int) $userId);
    }
}
