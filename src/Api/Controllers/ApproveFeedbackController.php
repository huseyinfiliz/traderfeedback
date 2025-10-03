<?php
namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use Flarum\Notification\Notification;
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
        
        // Önceden onaylı mıydı kontrol et
        $wasApproved = $feedback->is_approved;
        
        // Onaylayan kişiyi kaydet
        $feedback->is_approved = true;
        $feedback->approved_by_id = $actor->id;
        $feedback->save();
        
        // ✅ Database'den FRESH olarak yeniden çek
        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);
        
        // Stats güncelle
        $this->updateUserStats($feedback->to_user_id);
        
        // İlk kez onaylanıyorsa bildirimleri gönder
        if (!$wasApproved) {
            // 1. Feedback sahibine onay bildirimi - RAW QUERY
            if ($feedback->fromUser) {
                $now = Carbon::now();
                
                // ✅ app('db') kullan, facade değil
                app('db')->table('notifications')->insert([
                    'user_id' => $feedback->fromUser->id,
                    'from_user_id' => $feedback->to_user_id,
                    'type' => 'feedbackApproved',
                    'subject_id' => $feedback->id,
                    'data' => json_encode([
                        'feedbackId' => $feedback->id,
                        'feedbackType' => $feedback->type
                    ]),
                    'created_at' => $now,
                    'read_at' => null,
                    'is_deleted' => 0
                ]);
                
                app('log')->info('Raw inserted FeedbackApproved notification', [
                    'user_id' => $feedback->fromUser->id,
                    'from_user_id' => $feedback->to_user_id,
                    'subject_id' => $feedback->id
                ]);
            }
            
            // 2. Feedback alan kişiye yeni feedback bildirimi - Normal sync (çalışıyor)
            if ($feedback->toUser) {
                $notifications = app(NotificationSyncer::class);
                $newFeedbackBlueprint = new NewFeedbackBlueprint($feedback);
                $notifications->sync($newFeedbackBlueprint, [$feedback->toUser]);
            }
        }
        
        return $feedback;
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