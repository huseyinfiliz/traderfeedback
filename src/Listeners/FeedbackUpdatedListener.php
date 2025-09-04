<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;

class FeedbackUpdatedListener
{
    /**
     * @param FeedbackUpdated $event
     */
    public function handle(FeedbackUpdated $event)
    {
        $feedback = $event->feedback;
        $toUser = $feedback->toUser;
        
        // Recalculate trader stats
        $positiveCount = $toUser->feedbacksReceived()->where('type', 'positive')->count();
        $neutralCount = $toUser->feedbacksReceived()->where('type', 'neutral')->count();
        $negativeCount = $toUser->feedbacksReceived()->where('type', 'negative')->count();
        
        $stats = TraderStats::firstOrNew(['user_id' => $toUser->id]);
        $stats->positive_count = $positiveCount;
        $stats->neutral_count = $neutralCount;
        $stats->negative_count = $negativeCount;
        
        // Calculate score (percentage of positive feedback)
        $total = $positiveCount + $neutralCount + $negativeCount;
        $stats->score = $total > 0 ? ($positiveCount / $total) * 100 : 0;
        $stats->last_updated = now();
        $stats->save();
    }
}