<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\Notification\Event\Sending;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;

class NotificationSendingListener
{
    public function handle(Sending $event)
    {
        $blueprint = $event->blueprint;
        
        // Approve/Reject için - PUBLIC PROPERTY kullan
        if ($blueprint instanceof FeedbackApprovedBlueprint || 
            $blueprint instanceof FeedbackRejectedBlueprint) {
            
            foreach ($event->notifications as $notification) {
                // ✅ Metod yerine DOĞRUDAN property kullan
                $notification->from_user_id = $blueprint->toUserId;
                $notification->data = json_encode([
                    'feedbackId' => $blueprint->feedbackId,
                    'feedbackType' => $blueprint->feedbackType
                ]);
            }
        }
        
        // NewFeedback için - mevcut kod çalışıyor, dokunma
        if ($blueprint instanceof NewFeedbackBlueprint) {
            foreach ($event->notifications as $notification) {
                $data = $blueprint->getData();
                if (is_array($data) && !empty($data)) {
                    $notification->data = json_encode($data);
                }
                
                $fromUser = $blueprint->getFromUser();
                if ($fromUser) {
                    $notification->from_user_id = $fromUser->id;
                }
            }
        }
    }
}