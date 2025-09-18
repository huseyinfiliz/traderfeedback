<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Symfony\Contracts\Translation\TranslatorInterface;
use Illuminate\Support\Str;

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
     */
    public function getSubject()
    {
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
        if (!$this->feedback->approved_by_id) {
            return User::find(1) ?: null;
        }
        
        if (!$this->feedback->relationLoaded('approvedBy')) {
            $this->feedback->load('approvedBy');
        }
        
        return $this->feedback->approvedBy;
    }

    /**
     * {@inheritdoc}
     * NotificationHub gibi basit string veriler döndür
     */
    public function getData()
    {
        // toUser relationship'ini yükle
        if (!$this->feedback->relationLoaded('toUser')) {
            $this->feedback->load('toUser');
        }
        
        $toUser = $this->feedback->toUser;
        
        // NotificationHub formatında veri döndür
        return [
            'toUsername' => (string) ($toUser ? $toUser->username : 'Unknown'),
            'toUserId' => (string) $this->feedback->to_user_id,
            'feedbackType' => (string) $this->feedback->type,
            'unique' => (string) Str::orderedUuid(),
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