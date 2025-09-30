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
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use Flarum\User\User;
use Carbon\Carbon;

class RejectFeedbackController extends AbstractShowController
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
        
        // Reddeden kişiyi kaydet
        $feedback->approved_by_id = $actor->id;
        $feedback->save();
        
        // to_user_id'yi sakla (stats güncelleme için)
        $toUserId = $feedback->to_user_id;
        $fromUserId = $feedback->from_user_id;
        
        // BİLDİRİM GÖNDER - Silmeden önce!
        $fromUser = User::find($fromUserId);
        
        if ($fromUser) {
            $notifications = app(NotificationSyncer::class);
            $blueprint = new FeedbackRejectedBlueprint($feedback);
            $notifications->sync($blueprint, [$fromUser]);
        }
        
        // Şimdi güvenle silebiliriz
        $feedback->delete();
        
        // Stats güncelle
        $this->updateUserStats($toUserId);
        
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