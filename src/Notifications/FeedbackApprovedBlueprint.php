<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackApprovedBlueprint implements BlueprintInterface
{
    /**
     * @var Feedback
     */
    public $feedback;

    /**
     * @param Feedback $feedback
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->feedback;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromUser()
    {
        // Eğer approved_by_id yoksa, sistem kullanıcısını döndür
        if (!$this->feedback->approved_by_id) {
            return User::find(1) ?: null;
        }
        
        // approvedBy relationship'ini yükle
        if (!$this->feedback->relationLoaded('approvedBy')) {
            $this->feedback->load('approvedBy');
        }
        
        return $this->feedback->approvedBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        // toUser relationship'ini yükle
        if (!$this->feedback->relationLoaded('toUser')) {
            $this->feedback->load('toUser');
        }
        
        return [
            'feedbackType' => $this->feedback->type,
            'feedbackRole' => $this->feedback->role,
            'toUsername' => $this->feedback->toUser ? $this->feedback->toUser->username : null,
            'toUserId' => $this->feedback->to_user_id
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getType()
    {
        return 'feedbackApproved';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubjectModel()
    {
        return Feedback::class;
    }
    
    /**
     * Get the users that should receive the notification.
     *
     * @return array
     */
    public function getRecipients()
    {
        // Feedback veren kullanıcıya bildirim gönder
        return $this->feedback->fromUser ? [$this->feedback->fromUser] : [];
    }
}