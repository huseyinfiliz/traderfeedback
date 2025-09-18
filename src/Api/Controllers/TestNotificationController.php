<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\User\User;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;

class TestNotificationController extends AbstractShowController
{
    // Serializer'ı FeedbackSerializer yap
    public $serializer = FeedbackSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        
        // Test için actor'ı kullan
        $fromUserId = $actor->id;
        $toUserId = $actor->id === 1 ? 2 : 1; // Eğer admin ise user 2'ye, değilse admin'e gönder
        
        // Test feedback oluştur
        $feedback = new Feedback();
        $feedback->from_user_id = $fromUserId;
        $feedback->to_user_id = $toUserId;
        $feedback->type = 'positive';
        $feedback->comment = 'Test notification - ' . date('Y-m-d H:i:s');
        $feedback->role = 'buyer';
        $feedback->is_approved = true;
        $feedback->save();
        
        // İlişkileri yükle
        $feedback->load(['fromUser', 'toUser']);

        // Bildirim gönder
        $toUser = User::find($toUserId);
        if (!$toUser) {
            throw new \Exception('User not found: ' . $toUserId);
        }
        
        try {
            $notifications = app(NotificationSyncer::class);
            $notifications->sync(
                new NewFeedbackBlueprint($feedback),
                [$toUser]
            );
            
            // Log ekle
            app('log')->info('Test notification sent', [
                'feedback_id' => $feedback->id,
                'from_user' => $fromUserId,
                'to_user' => $toUserId,
                'type' => 'newFeedback'
            ]);
            
        } catch (\Exception $e) {
            app('log')->error('Test notification error: ' . $e->getMessage());
            throw $e;
        }
        
        // Feedback'i döndür (serializer bunu işleyecek)
        return $feedback;
    }
}