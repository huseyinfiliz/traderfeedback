<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\AlertableInterface;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackRejectedBlueprint implements BlueprintInterface, AlertableInterface
{
    public function __construct(public Feedback $feedback)
    {
    }

    public function getSubject(): ?\Flarum\Database\AbstractModel
    {
        // ✅ DÜZELTME: Notification'ın KONUSU olan entity'yi döndür (Feedback modeli)
        // Bu subject_id'ye yazılır (subject_id = feedback_id olur)
        return $this->feedback;
    }

    public function getFromUser(): ?\Flarum\User\User
    {
        // Bildirimin KAYNAĞI (from_user_id sütununa yazılacak)
        // Frontend notification.fromUser() ile bu kişiyi gösterecek
        return User::find($this->feedback->to_user_id);
    }

    public function getData(): mixed
    {
        $toUser = $this->feedback->toUser ?? User::find($this->feedback->to_user_id);
        $toUserSlug = $toUser ? \HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer::getUserSlug($toUser) : null;

        return [
            'feedbackId'   => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
            'toUserId'     => $this->feedback->to_user_id,
            'toUserSlug'   => $toUserSlug ?: ($toUser?->username ?? (string) $this->feedback->to_user_id),
        ];
    }

    public static function getType(): string
    {
        return 'feedbackRejected';
    }

    public static function getSubjectModel(): string
    {
        // ✅ DÜZELTME: Subject model artık Feedback
        return Feedback::class;
    }
}
