<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Database\AbstractModel;
use Flarum\Notification\AlertableInterface;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackNeedsApprovalBlueprint implements BlueprintInterface, AlertableInterface
{
    public function __construct(public Feedback $feedback)
    {
    }

    public function getSubject(): ?AbstractModel
    {
        return $this->feedback;
    }

    public function getFromUser(): ?User
    {
        return $this->feedback->fromUser ?? User::find($this->feedback->from_user_id);
    }

    public function getData(): mixed
    {
        $toUser = $this->feedback->toUser ?? User::find($this->feedback->to_user_id);
        $fromUser = $this->getFromUser();

        $toUserSlug = $toUser ? \HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer::getUserSlug($toUser) : null;

        return [
            'feedbackId'   => $this->feedback->id,
            'fromUserId'   => $this->feedback->from_user_id,
            'fromUserName' => $fromUser?->display_name ?? $fromUser?->username,
            'toUserId'     => $this->feedback->to_user_id,
            'toUserSlug'   => $toUserSlug ?: ($toUser?->username ?? (string) $this->feedback->to_user_id),
            'toUserName'   => $toUser?->display_name ?? $toUser?->username,
            'feedbackType' => $this->feedback->type,
            'role'         => $this->feedback->role,
            'comment'      => mb_substr($this->feedback->comment, 0, 50).'...',
        ];
    }

    public static function getType(): string
    {
        return 'feedbackNeedsApproval';
    }

    public static function getSubjectModel(): string
    {
        return Feedback::class;
    }
}
