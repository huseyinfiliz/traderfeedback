<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\User\Event\Deleted;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;

class UserDeletedListener
{
    /**
     * @param Deleted $event
     */
    public function handle(Deleted $event)
    {
        $user = $event->user;

        // Delete all feedback given by this user
        Feedback::where('from_user_id', $user->id)->delete();

        // Delete all feedback received by this user
        Feedback::where('to_user_id', $user->id)->delete();

        // Delete trader stats for this user
        TraderStats::where('user_id', $user->id)->delete();
    }
}