<?php
namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use Carbon\Carbon;

class RejectFeedbackController extends AbstractShowController
{
    public $serializer = FeedbackSerializer::class;
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);
        
        // Değerleri sakla (silmeden önce)
        $toUserId = $feedback->to_user_id;
        $fromUserId = $feedback->from_user_id;
        $feedbackId = $feedback->id;
        $feedbackType = $feedback->type;
        
        // RAW QUERY ile bildirim oluştur (feedback silinmeden önce)
        if ($fromUserId) {
            $now = Carbon::now();
            
            app('db')->table('notifications')->insert([
                'user_id' => $fromUserId,
                'from_user_id' => $toUserId,
                'type' => 'feedbackRejected',
                'subject_id' => $feedbackId,
                'data' => json_encode([
                    'feedbackId' => $feedbackId,
                    'feedbackType' => $feedbackType
                ]),
                'created_at' => $now,
                'read_at' => null,
                'is_deleted' => 0
            ]);
            
            app('log')->info('Raw inserted FeedbackRejected notification', [
                'user_id' => $fromUserId,
                'from_user_id' => $toUserId,
                'subject_id' => $feedbackId
            ]);
        }
        
        // Feedback'i sil
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