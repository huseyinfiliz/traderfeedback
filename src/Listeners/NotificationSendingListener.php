<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\Notification\Event\Sending;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;

/**
 * ÇÖZÜM: Bu listener notification göndermeden ÖNCE çalışır
 * ve Blueprint'ten data + from_user_id'yi alıp notification'a yazar
 */
class NotificationSendingListener
{
    public function handle(Sending $event)
    {
        $blueprint = $event->blueprint;
        
        // Sadece bizim notification tiplerinde çalış
        if ($blueprint instanceof FeedbackApprovedBlueprint ||
            $blueprint instanceof FeedbackRejectedBlueprint ||
            $blueprint instanceof NewFeedbackBlueprint) {
            
            foreach ($event->notifications as $notification) {
                // 1. DATA'yı al ve JSON encode et
                $data = $blueprint->getData();
                if (is_array($data) && !empty($data)) {
                    $notification->data = json_encode($data);
                }
                
                // 2. FROM_USER_ID'yi Blueprint'ten al ve zorla set et
                // Bu kritik çünkü Flarum NotificationSyncer bunu görmezden geliyor
                $fromUser = $blueprint->getFromUser();
                if ($fromUser) {
                    $notification->from_user_id = $fromUser->id;
                }
                
                app('log')->info('NotificationSendingListener: Fixed notification', [
                    'notification_type' => $notification->type,
                    'data' => $notification->data,
                    'from_user_id' => $notification->from_user_id,
                    'user_id' => $notification->user_id
                ]);
            }
        }
    }
}