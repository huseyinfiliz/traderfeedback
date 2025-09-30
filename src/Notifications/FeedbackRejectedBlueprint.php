<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackRejectedBlueprint implements BlueprintInterface
{
    public $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function getSubject()
    {
        // ✅ DÜZELTME: Notification'ın KONUSU olan entity'yi döndür (Feedback modeli)
        // Bu subject_id'ye yazılır (subject_id = feedback_id olur)
        return $this->feedback;
    }

    public function getFromUser()
    {
        // Bildirimin KAYNAĞI (from_user_id sütununa yazılacak)
        // Frontend notification.fromUser() ile bu kişiyi gösterecek
        return User::find($this->feedback->to_user_id);
    }

    public function getData()
    {
        // Sadeleştirilmiş data - frontend fromUser'dan username'i alacak
        return [
            'feedbackId' => $this->feedback->id,
            'feedbackType' => $this->feedback->type
        ];
    }

    public static function getType()
    {
        return 'feedbackRejected';
    }

    public static function getSubjectModel()
    {
        // ✅ DÜZELTME: Subject model artık Feedback
        return Feedback::class;
    }
}