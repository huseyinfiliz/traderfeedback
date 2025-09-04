<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use Flarum\Notification\NotificationSyncer;
use Carbon\Carbon; // Carbon import eklendi

class FeedbackCreatedListener
{
    /**
     * @var NotificationSyncer
     */
    protected $notifications;

    /**
     * @param NotificationSyncer $notifications
     */
    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * @param FeedbackCreated $event
     */
    public function handle(FeedbackCreated $event)
    {
        $feedback = $event->feedback;
        $toUser = $feedback->toUser;
        
        // Update trader stats
        $stats = TraderStats::firstOrNew(['user_id' => $toUser->id]);
        
        if ($feedback->type === 'positive') {
            $stats->positive_count++;
        } elseif ($feedback->type === 'negative') {
            $stats->negative_count++;
        } else {
            $stats->neutral_count++;
        }
        
        // Calculate score (percentage of positive feedback)
        $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
        $stats->score = $total > 0 ? ($stats->positive_count / $total) * 100 : 0;
        $stats->last_updated = Carbon::now(); // now() yerine Carbon::now()
        $stats->save();
        
        // Send notification to the user
        if ($feedback->is_approved) {
            $this->notifications->sync(
                new NewFeedbackBlueprint($feedback),
                [$toUser]
            );
        }
    }
}