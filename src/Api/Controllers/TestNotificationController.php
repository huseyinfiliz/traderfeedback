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
        $type = Arr::get($request->getQueryParams(), 'type', 'approved'); // approved veya rejected
        
        // Test için mevcut bir feedback bul
        $feedback = Feedback::where('is_approved', true)->first();
        
        if (!$feedback) {
            // Test feedback oluştur
            $feedback = new Feedback();
            $feedback->from_user_id = $actor->id;
            $feedback->to_user_id = $actor->id === 1 ? 2 : 1;
            $feedback->type = 'positive';
            $feedback->comment = 'Test notification - ' . date('Y-m-d H:i:s');
            $feedback->role = 'buyer';
            $feedback->is_approved = true;
            $feedback->approved_by_id = 1; // Admin
            $feedback->save();
        } else {
            // Mevcut feedback'i güncelle
            $feedback->approved_by_id = $actor->id;
            $feedback->save();
        }
        
        // İlişkileri yükle
        $feedback->load(['fromUser', 'toUser']);
        
        // Bildirimi gönder
        $fromUser = User::find($feedback->from_user_id);
        
        if (!$fromUser) {
            throw new \Exception('From user not found: ' . $feedback->from_user_id);
        }
        
        try {
            $notifications = app(NotificationSyncer::class);
            
            if ($type === 'rejected') {
                app('log')->info('Testing FeedbackRejected notification');
                $blueprint = new FeedbackRejectedBlueprint($feedback);
            } else {
                app('log')->info('Testing FeedbackApproved notification');
                $blueprint = new FeedbackApprovedBlueprint($feedback);
            }
            
            $result = $notifications->sync($blueprint, [$fromUser]);
            
            app('log')->info('Test notification result', [
                'type' => $type,
                'feedback_id' => $feedback->id,
                'from_user' => $fromUser->id,
                'result' => $result
            ]);
            
            // Veritabanını kontrol et
            $notificationCount = \Flarum\Notification\Notification::where('user_id', $fromUser->id)
                ->where('type', $type === 'rejected' ? 'feedbackRejected' : 'feedbackApproved')
                ->count();
            
            app('log')->info('Notifications in database', [
                'count' => $notificationCount,
                'type' => $type === 'rejected' ? 'feedbackRejected' : 'feedbackApproved'
            ]);
            
        } catch (\Exception $e) {
            app('log')->error('Test notification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
        return $feedback;
    }
}