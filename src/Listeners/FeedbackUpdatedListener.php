<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use Flarum\Notification\NotificationSyncer;
use Carbon\Carbon;

class FeedbackUpdatedListener
{
    protected $notifications;

    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(FeedbackUpdated $event)
    {
        $feedback = $event->feedback;
        $actor = $event->actor;

        if (!$feedback->is_approved) {
            return;
        }

        // İlişkileri yükle
        $feedback->loadMissing(['fromUser', 'toUser']);

        // Stats güncelle
        $this->updateUserStats($feedback->to_user_id);

        // Bildirim gönder
        if ($feedback->fromUser && $feedback->fromUser->id !== $actor->id) {
            $this->notifications->sync(
                new FeedbackApprovedBlueprint($feedback),
                [$feedback->fromUser]
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