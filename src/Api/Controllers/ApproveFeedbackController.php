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
                $blueprint = new FeedbackApprovedBlueprint($feedback);
                $notifications->sync($blueprint, [$fromUser]);
            }
            
            // 2. Feedback alan kişiye yeni feedback bildirimi
            $toUser = User::find($feedback->to_user_id);
            
            if ($toUser) {
                $newFeedbackBlueprint = new NewFeedbackBlueprint($feedback);
                $notifications->sync($newFeedbackBlueprint, [$toUser]);
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