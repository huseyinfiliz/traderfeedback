<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewFeedbackBlueprint implements BlueprintInterface, MailableInterface
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
     * Get the user who is the subject of this notification (who receives it)
     */
    public function getSubject()
    {
        // toUser relationship'ini yükle
        if (!$this->feedback->relationLoaded('toUser')) {
            $this->feedback->load('toUser');
        }
        
        // Notification'ı alacak kullanıcıyı döndür
        return $this->feedback->toUser;
    }

    /**
     * {@inheritdoc}
     * Get the user who triggered this notification
     */
    public function getFromUser()
    {
        // fromUser relationship'ini yükle
        if (!$this->feedback->relationLoaded('fromUser')) {
            $this->feedback->load('fromUser');
        }
        
        return $this->feedback->fromUser;
    }

    /**
     * {@inheritdoc}
     * Notification data
     */
    public function getData()
    {
        // Relationships'leri yükle
        if (!$this->feedback->relationLoaded('toUser')) {
            $this->feedback->load('toUser');
        }
        
        if (!$this->feedback->relationLoaded('fromUser')) {
            $this->feedback->load('fromUser');
        }
        
        return [
            'feedbackId' => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
            'feedbackRole' => $this->feedback->role,
            'feedbackComment' => substr($this->feedback->comment, 0, 100),
            'fromUserId' => $this->feedback->from_user_id,
            'fromUsername' => $this->feedback->fromUser ? $this->feedback->fromUser->username : 'Unknown',
            'fromDisplayName' => $this->feedback->fromUser ? $this->feedback->fromUser->display_name : 'Unknown',
            'toUserId' => $this->feedback->to_user_id,
            'toUsername' => $this->feedback->toUser ? $this->feedback->toUser->username : 'Unknown',
            'createdAt' => $this->feedback->created_at ? $this->feedback->created_at->toIso8601String() : null
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getType()
    {
        return 'newFeedback';
    }

    /**
     * {@inheritdoc}
     * The model that is the subject of the notification
     */
    public static function getSubjectModel()
    {
        // Subject User olduğu için User::class döndürmeliyiz
        return User::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailView()
    {
        return ['text' => 'huseyinfiliz-traderfeedback::emails.newFeedback'];
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSubject(TranslatorInterface $translator)
    {
        $fromUser = $this->getFromUser();
        $username = $fromUser ? $fromUser->display_name : 'Someone';
        
        return $translator->trans('huseyinfiliz-traderfeedback.email.new_feedback_subject', [
            '{username}' => $username
        ]);
    }
}