<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class NewFeedbackBlueprint implements BlueprintInterface
{
    public $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function getSubject()
    {
        return User::find($this->feedback->to_user_id);
    }

    public function getFromUser()
    {
        return User::find($this->feedback->from_user_id);
    }

    public function getData()
    {
        // Frontend'in beklediği data formatı
        return [
            'feedbackId' => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
            'role' => $this->feedback->role,
            'comment' => substr($this->feedback->comment, 0, 50) . '...' // İlk 50 karakter
        ];
    }

    public static function getType()
    {
        return 'newFeedback';
    }

    public static function getSubjectModel()
    {
        return User::class;
    }
}