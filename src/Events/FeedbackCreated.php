<?php

namespace HuseyinFiliz\TraderFeedback\Events;

use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Flarum\User\User;

class FeedbackCreated
{
    /**
     * @var Feedback
     */
    public $feedback;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param Feedback $feedback
     * @param User $actor
     */
    public function __construct(Feedback $feedback, User $actor)
    {
        $this->feedback = $feedback;
        $this->actor = $actor;
    }
}