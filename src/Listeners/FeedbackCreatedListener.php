<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use Flarum\Notification\NotificationSyncer;
use Carbon\Carbon;

class FeedbackCreatedListener
{
    protected $notifications;

    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(FeedbackCreated $event)
    {
        $feedback = $event->feedback;
        
        // İlişkileri yükle
        if (!$feedback->relationLoaded('toUser')) {
            $feedback->load('toUser');
        }
        
        // SADECE ONAYLI İSE İŞLEM YAP
        if ($feedback->is_approved) {
            // Stats güncelle
            $this->updateUserStats($feedback->to_user_id);
            
            // Bildirim gönder
            if ($feedback->toUser && $feedback->toUser->id !== $feedback->from_user_id) {
                app('log')->info('Sending newFeedback notification (approved)', [
                    'feedback_id' => $feedback->id,
                    'to_user' => $feedback->toUser->id,
                    'is_approved' => $feedback->is_approved
                ]);
                
                $this->notifications->sync(
                    new NewFeedbackBlueprint($feedback),
                    [$feedback->toUser]
                );
            }
        } else {
            // ONAYLI DEĞİLSE BİLDİRİM GÖNDERME!
            app('log')->info('Feedback not approved, skipping notification', [
                'feedback_id' => $feedback->id,
                'is_approved' => $feedback->is_approved
            ]);
        }
    }
    
    protected function updateUserStats($userId)
    {
        $stats = TraderStats::firstOrNew(['user_id' => $userId]);
        
        $feedbacks = \HuseyinFiliz\TraderFeedback\Models\Feedback::where('to_user_id', $userId)
            ->where('is_approved', true)
            ->get();
        
        $stats->positive_count = $feedbacks->where('type', 'positive')->count();
        $stats->negative_count = $feedbacks->where('type', 'negative')->count();
        $stats->neutral_count = $feedbacks->where('type', 'neutral')->count();
        
        $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
        $stats->score = $total > 0 ? ($stats->positive_count / $total) * 100 : 0;
        $stats->last_updated = Carbon::now();
        $stats->save();
    }
}