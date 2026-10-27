<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class UserDeletingListener
{
    public function handle(User $user): void
    {
        // Foreign keys cascade before the domain Deleted event is dispatched.
        $user->setRelation('traderFeedbackRecipientIds', Feedback::where('from_user_id', $user->id)
            ->where('is_approved', true)->where('to_user_id', '!=', $user->id)
            ->distinct()->pluck('to_user_id'));
    }
}
