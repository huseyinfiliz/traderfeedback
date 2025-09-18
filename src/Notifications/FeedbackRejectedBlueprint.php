<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Symfony\Contracts\Translation\TranslatorInterface;
use Illuminate\Support\Str;

/**
 * Notification blueprint for rejected feedback.
 *
 * This implementation avoids using User::find(1) as a fallback for the
 * from_user_id. When no approved_by_id is set on the feedback (which
 * indicates the moderator who rejected it), getFromUser() now returns
 * null. The rejection controller assigns approved_by_id, so in normal
 * operation this method will return the moderator who performed the
 * rejection.
 */
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
        // If no approved_by_id has been recorded, there is no clear
        // moderator associated with the rejection, so return null. This
        // prevents Flarum from defaulting to user ID 1.
        if (!$this->feedback->approved_by_id) {
            return null;
        }

        if (!$this->feedback->relationLoaded('approvedBy')) {
            $this->feedback->load('approvedBy');
        }

        return $this->feedback->approvedBy;
    }

    /**
     * {@inheritdoc}
     * NotificationHub compatible data.
     */
    public function getData()
    {
        // Ensure the toUser relationship is loaded
        if (!$this->feedback->relationLoaded('toUser')) {
            $this->feedback->load('toUser');
        }

        $toUser = $this->feedback->toUser;

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