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
        // Feedback'i veren kişiye bildirim gidecek
        return User::find($this->feedback->from_user_id);
    }

    public function getFromUser()
    {
        // Onaylayan moderatör
        $approver = User::find($this->feedback->approved_by_id);
        return $approver ?: User::find(1); // Admin fallback
    }

    public function getData()
    {
        $toUser = User::find($this->feedback->to_user_id);
        
        return [
            'feedbackId' => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
            'toUsername' => $toUser ? $toUser->username : 'Unknown',
            'toUserId' => $this->feedback->to_user_id
        ];
    }

    public static function getType()
    {
        return 'feedbackApproved';
    }

    public static function getSubjectModel()
    {
        return User::class;
    }
}