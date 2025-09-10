<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeedbackRejectedBlueprint implements BlueprintInterface, MailableInterface
{
    /**
     * @var Feedback
     */
    protected $feedback;

    /**
     * @param Feedback $feedback
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * {@inheritdoc}
     * Get the user who is the subject of this notification (feedback giver)
     */
    public function getSubject()
    {
        // fromUser relationship'ini yükle - feedback veren kullanıcıya bildirim gidecek
        if (!$this->feedback->relationLoaded('fromUser')) {
            $this->feedback->load('fromUser');
        }
        
        return $this->feedback->fromUser;
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
        
        // fromUser relationship'ini yükle
        if (!$this->feedback->relationLoaded('fromUser')) {
            $this->feedback->load('fromUser');
        }
        
        return [
            'feedbackId' => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
            'feedbackRole' => $this->feedback->role,
            'toUserId' => $this->feedback->to_user_id,
            'toUsername' => $this->feedback->toUser ? $this->feedback->toUser->username : 'Unknown',
            'toDisplayName' => $this->feedback->toUser ? $this->feedback->toUser->display_name : 'Unknown',
            'rejectedAt' => $this->feedback->updated_at ? $this->feedback->updated_at->toIso8601String() : null
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getType()
    {
        return 'feedbackRejected';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubjectModel()
    {
        return User::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailView()
    {
        return ['text' => 'huseyinfiliz-traderfeedback::emails.feedbackRejected'];
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSubject(TranslatorInterface $translator)
    {
        return $translator->trans('huseyinfiliz-traderfeedback.email.feedback_rejected_subject');
    }
}