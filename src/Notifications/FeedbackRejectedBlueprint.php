<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\AlertableInterface;
use Flarum\Notification\Blueprint\BlueprintInterface;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackRejectedBlueprint implements BlueprintInterface, AlertableInterface
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
        return $this->feedback->toUser;
    }

    public function getData(): mixed
    {
        $toUser = $this->feedback->toUser;
        $toUserSlug = $toUser ? \HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer::getUserSlug($toUser) : null;

        return [
            'feedbackId'   => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
            'toUserId'     => $this->feedback->to_user_id,
            'toUserSlug'   => $toUserSlug ?: ($toUser->username ?? (string) $this->feedback->to_user_id),
        ];
    }

    public static function getType(): string
    {
        return 'feedbackRejected';
    }

    public static function getSubjectModel(): string
    {
        return Feedback::class;
    }
}
