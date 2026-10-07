<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\AlertableInterface;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackApprovedBlueprint implements BlueprintInterface, AlertableInterface
{
    public function __construct(public Feedback $feedback)
    {
    }

    public function getSubject(): ?\Flarum\Database\AbstractModel
    {
        return $this->feedback;
    }

    public function getFromUser(): ?\Flarum\User\User
    {
        return User::find($this->feedback->to_user_id);
    }

    public function getData(): mixed
    {
        return [
            'feedbackId'   => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
        ];
    }

    public static function getType(): string
    {
        return 'feedbackApproved';
    }

    public static function getSubjectModel(): string
    {
        return Feedback::class;
    }
}
