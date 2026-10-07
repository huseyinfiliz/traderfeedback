<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\AlertableInterface;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class NewFeedbackBlueprint implements BlueprintInterface, AlertableInterface
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
        return User::find($this->feedback->from_user_id);
    }

    public function getData(): mixed
    {
        // Frontend'in beklediği data formatı
        return [
            'feedbackId'   => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
            'role'         => $this->feedback->role,
            'comment'      => substr($this->feedback->comment, 0, 50).'...', // İlk 50 karakter
        ];
    }

    public static function getType(): string
    {
        return 'newFeedback';
    }

    public static function getSubjectModel(): string
    {
        // ✅ DÜZELTME: Subject model artık Feedback
        return Feedback::class;
    }
}
