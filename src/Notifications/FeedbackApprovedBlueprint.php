<?php
namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackApprovedBlueprint implements BlueprintInterface
{
    public $feedback;

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
        return User::find($this->feedback->to_user_id);
    }

    public function getData()
    {
        return [
            'feedbackId' => $this->feedback->id,
            'feedbackType' => $this->feedback->type
        ];
    }

    public static function getType()
    {
        return 'feedbackApproved';
    }

    public static function getSubjectModel()
    {
        return Feedback::class;
    }
}