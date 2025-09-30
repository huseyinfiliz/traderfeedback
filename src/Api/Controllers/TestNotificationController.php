<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\User\User;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use Illuminate\Support\Arr;

class TestNotificationController extends AbstractShowController
{
    public $serializer = FeedbackSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $type = Arr::get($request->getQueryParams(), 'type', 'approved');
        
        // İki farklı kullanıcı bul
        $user1 = User::where('id', '!=', 1)->first();
        $user2 = User::where('id', '!=', 1)->where('id', '!=', optional($user1)->id)->first();
        
        if (!$user1 || !$user2) {
            // Fallback: Mevcut bir feedback bul
            $feedback = Feedback::where('is_approved', true)->first();
            
            if (!$feedback) {
                $feedback = new Feedback();
                $feedback->from_user_id = $actor->id;
                $feedback->to_user_id = $actor->id === 1 ? ($user1 ? $user1->id : $actor->id) : 1;
                $feedback->type = 'positive';
                $feedback->comment = 'Test notification - ' . date('Y-m-d H:i:s');
                $feedback->role = 'buyer';
                $feedback->is_approved = true;
                $feedback->approved_by_id = $actor->id;
                $feedback->save();
            }
        } else {
            // Test feedback oluştur veya bul
            $feedback = Feedback::where('from_user_id', $user1->id)
                ->where('to_user_id', $user2->id)
                ->first();
            
            if (!$feedback) {
                $feedback = new Feedback();
                $feedback->from_user_id = $user1->id;
                $feedback->to_user_id = $user2->id;
                $feedback->type = 'positive';
                $feedback->comment = 'Test notification - ' . date('Y-m-d H:i:s');
                $feedback->role = 'buyer';
                $feedback->is_approved = true;
                $feedback->approved_by_id = $actor->id;
                $feedback->save();
            }
        }
        
        // İlişkileri yükle
        $feedback->load(['fromUser', 'toUser']);
        
        // Bildirimi alacak kişi: feedback veren
        $recipient = User::find($feedback->from_user_id);
        
        try {
            $notifications = app(NotificationSyncer::class);
            
            if ($type === 'rejected') {
                $blueprint = new FeedbackRejectedBlueprint($feedback);
            } else {
                $blueprint = new FeedbackApprovedBlueprint($feedback);
            }
            
            // NotificationSyncer çağır - NotificationSendingListener otomatik çalışacak
            $result = $notifications->sync($blueprint, [$recipient]);
            
            app('log')->info('Test notification sent', [
                'type' => $type,
                'feedback_id' => $feedback->id,
                'recipient_id' => $recipient->id,
                'result' => $result
            ]);
            
            // Veritabanını kontrol et
            $notificationType = $type === 'rejected' ? 'feedbackRejected' : 'feedbackApproved';
            $lastNotification = \Flarum\Notification\Notification::where('user_id', $recipient->id)
                ->where('type', $notificationType)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($lastNotification) {
                app('log')->info('Last notification in database', [
                    'id' => $lastNotification->id,
                    'user_id' => $lastNotification->user_id,
                    'from_user_id' => $lastNotification->from_user_id,
                    'subject_id' => $lastNotification->subject_id,
                    'data' => $lastNotification->data
                ]);
            }
            
        } catch (\Exception $e) {
            app('log')->error('Test notification error', [
                'error' => $e->getMessage()
            ]);
        }
        
        return $feedback;
    }
}