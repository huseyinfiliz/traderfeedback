<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\User\Event\Deleted;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class UserDeletedListener
{
    public function __construct(protected StatsService $stats)
    {
    }

    /**
     * @param Deleted $event
     */
    public function handle(Deleted $event)
    {
        $user = $event->user;

        $affectedRecipientIds = $user->getRelation('traderFeedbackRecipientIds')->all();

        // Delete all feedback given by this user
        Feedback::where('from_user_id', $user->id)->delete();

        // Delete all feedback received by this user
        Feedback::where('to_user_id', $user->id)->delete();

        // Delete trader stats and clear cache for this user
        TraderStats::where('user_id', $user->id)->delete();
        $this->stats->clearCache($user->id);

        // Recalculate stats for all affected recipients
        foreach ($affectedRecipientIds as $recipientId) {
            $this->stats->updateUserStats((int) $recipientId);
        }
    }
}
