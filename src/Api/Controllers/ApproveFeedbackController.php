<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use Carbon\Carbon;
use Flarum\User\User;

class ApproveFeedbackController extends AbstractShowController
{
    public $serializer = FeedbackSerializer::class;
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        $feedback = Feedback::findOrFail($id);
        
        // İlişkileri yükle
        $feedback->load(['fromUser', 'toUser']);
        
        // Önceden onaylı mıydı kontrol et
        $wasApproved = $feedback->is_approved;
        
        // Onaylayan kişiyi kaydet
        $feedback->is_approved = true;
        $feedback->approved_by_id = $actor->id;
        $feedback->save();
        
        // Stats güncelle
        $this->updateUserStats($feedback->to_user_id);
        
        // İlk kez onaylanıyorsa bildirimleri gönder
        if (!$wasApproved) {
            $notifications = app(NotificationSyncer::class);
            
            // 1. Feedback sahibine onay bildirimi
            $fromUser = User::find($feedback->from_user_id);
            
            if ($fromUser) {
                try {
                    app('log')->info('Attempting to send FeedbackApproved notification', [
                        'feedback_id' => $feedback->id,
                        'recipient_id' => $fromUser->id,
                        'sender_id' => $actor->id
                    ]);
                    
                    $blueprint = new FeedbackApprovedBlueprint($feedback);
                    $result = $notifications->sync($blueprint, [$fromUser]);
                    
                    // ÇÖZÜM: subject_id (feedback ID) ile doğru bildirimi bul ve güncelle
                    // Queue'dan dolayı biraz bekle
                    sleep(1); // 1 saniye bekle
                    $this->fixNotificationBySubjectId($feedback->id, 'feedbackApproved', $feedback->to_user_id);
                    
                    app('log')->info('FeedbackApproved notification sync result', [
                        'result' => $result
                    ]);
                } catch (\Exception $e) {
                    app('log')->error('FeedbackApproved notification failed', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // 2. Feedback alan kişiye yeni feedback bildirimi
            $toUser = User::find($feedback->to_user_id);
            
            if ($toUser) {
                try {
                    app('log')->info('Attempting to send NewFeedback notification', [
                        'feedback_id' => $feedback->id,
                        'recipient_id' => $toUser->id,
                        'sender_id' => $feedback->from_user_id
                    ]);
                    
                    $newFeedbackBlueprint = new NewFeedbackBlueprint($feedback);
                    $result = $notifications->sync($newFeedbackBlueprint, [$toUser]);
                    
                    app('log')->info('NewFeedback notification sync result', [
                        'result' => $result
                    ]);
                } catch (\Exception $e) {
                    app('log')->error('NewFeedback notification failed', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return $feedback;
    }
    
    /**
     * subject_id kullanarak doğru notification'ı bul ve from_user_id'yi düzelt
     */
    private function fixNotificationBySubjectId($feedbackId, $notificationType, $correctFromUserId)
    {
        try {
            // subject_id ile notification'ı bul
            // subject_id feedback ID'sini tutuyor
            $updated = app('db')->table('notifications')
                ->where('subject_id', $feedbackId)
                ->where('type', $notificationType)
                ->where('from_user_id', '!=', $correctFromUserId)
                ->update(['from_user_id' => $correctFromUserId]);
            
            if ($updated) {
                app('log')->info('Fixed notification from_user_id using subject_id', [
                    'subject_id' => $feedbackId,
                    'type' => $notificationType,
                    'new_from_user_id' => $correctFromUserId,
                    'updated_count' => $updated
                ]);
            } else {
                app('log')->info('No notification found to fix or already correct', [
                    'subject_id' => $feedbackId,
                    'type' => $notificationType
                ]);
            }
        } catch (\Exception $e) {
            app('log')->error('Failed to fix notification from_user_id', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    protected function updateUserStats($userId)
    {
        $stats = TraderStats::firstOrNew(['user_id' => $userId]);
        
        $stats->positive_count = Feedback::where('to_user_id', $userId)
            ->where('type', 'positive')
            ->where('is_approved', true)
            ->count();
            
        $stats->neutral_count = Feedback::where('to_user_id', $userId)
            ->where('type', 'neutral')
            ->where('is_approved', true)
            ->count();
            
        $stats->negative_count = Feedback::where('to_user_id', $userId)
            ->where('type', 'negative')
            ->where('is_approved', true)
            ->count();
        
        $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
        $stats->score = $total > 0 ? ($stats->positive_count / $total) * 100 : 0;
        $stats->last_updated = Carbon::now();
        $stats->save();
    }
}