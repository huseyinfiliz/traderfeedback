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
        
        // Stats güncelle (sadece onaylıysa)
        if ($feedback->is_approved) {
            $this->updateUserStats($feedback->to_user_id);
        }
        
        // Bildirim gönder
        if ($feedback->toUser && $feedback->toUser->id !== $feedback->from_user_id) {
            $this->notifications->sync(
                new NewFeedbackBlueprint($feedback),
                [$feedback->toUser]
            );
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