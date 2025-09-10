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
        // fromUser relationship'ini yükle
        if (!$this->feedback->relationLoaded('fromUser')) {
            $this->feedback->load('fromUser');
        }
        
        return $this->feedback->fromUser;
    }

    public function getData()
    {
        return [
            'feedbackType' => $this->feedback->type,
            'feedbackRole' => $this->feedback->role,
            'fromUsername' => $this->feedback->fromUser ? $this->feedback->fromUser->username : null,
            'fromUserId' => $this->feedback->from_user_id
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
        // Feedback alan kullanıcıya bildirim gönder
        if (!$this->feedback->relationLoaded('toUser')) {
            $this->feedback->load('toUser');
        }
        
        return $this->feedback->toUser ? [$this->feedback->toUser] : [];
    }
}