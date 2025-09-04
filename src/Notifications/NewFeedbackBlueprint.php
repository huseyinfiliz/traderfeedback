<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class NewFeedbackBlueprint implements BlueprintInterface
{
    protected $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function getSubject()
    {
        return $this->feedback;
    }

    public function getFromUser()
    {
        return $this->feedback->fromUser;
    }

    public function getData()
    {
        return [
            'feedbackType' => $this->feedback->type,
            'role' => $this->feedback->role
        ];
    }

    public static function getType()
    {
        return 'newFeedback';
    }

    public static function getSubjectModel()
    {
        return Feedback::class;
    }

    public function getRecipients()
    {
        return [
            User::find($this->feedback->to_user_id)
        ];
    }
}