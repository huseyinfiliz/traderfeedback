<?php

namespace HuseyinFiliz\TraderFeedback\Events;

use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackCreated
{
    public function __construct(public Feedback $feedback, public User $actor)
    {
    }
}
