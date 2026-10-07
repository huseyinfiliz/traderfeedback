<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\User\Event\Deleted;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class UserDeletedListener
{
    /**
     * @param Deleted $event
     */
    public function handle(Deleted $event)
    {
        $user = $event->user;

        // Find recipient users of approved feedback given by this user before deletion
        $affectedRecipientIds = Feedback::where('from_user_id', $user->id)
            ->where('is_approved', true)
            ->pluck('to_user_id')
            ->unique()
            ->all();

        // Delete all feedback given by this user
        Feedback::where('from_user_id', $user->id)->delete();

        // Delete all feedback received by this user
        Feedback::where('to_user_id', $user->id)->delete();

        // Delete trader stats and clear cache for this user
        TraderStats::where('user_id', $user->id)->delete();
        StatsService::clearCache($user->id);

        // Recalculate stats for all affected recipients
        foreach ($affectedRecipientIds as $recipientId) {
            StatsService::updateUserStats((int) $recipientId);
        }
    }
}