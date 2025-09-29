<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackApprovedBlueprint implements BlueprintInterface
{
    public $feedback;
    private $toUser;
    private $fromUser;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
        
        // User'ları önceden yükle
        $this->fromUser = User::find($feedback->from_user_id);
        $this->toUser = User::find($feedback->to_user_id);
        
        // Debug log
        app('log')->info('FeedbackApprovedBlueprint created', [
            'feedback_id' => $feedback->id,
            'from_user_id' => $feedback->from_user_id,
            'to_user_id' => $feedback->to_user_id,
            'fromUser' => $this->fromUser ? $this->fromUser->id : null,
            'toUser' => $this->toUser ? $this->toUser->id : null
        ]);
    }

    public function getSubject()
    {
        // Bildirimi ALAN kişi (user_id sütununa yazılacak)
        app('log')->debug('FeedbackApproved getSubject returning', [
            'user_id' => $this->fromUser ? $this->fromUser->id : null
        ]);
        
        return $this->fromUser; // Feedback'i veren kişi bildirimi alır
    }

    public function getFromUser()
    {
        // Bildirimin KAYNAĞI (from_user_id sütununa yazılacak)
        // BU ÖNEMLİ: Frontend'de tıklandığında bu kişinin profiline gidecek
        app('log')->debug('FeedbackApproved getFromUser returning', [
            'user_id' => $this->toUser ? $this->toUser->id : null
        ]);
        
        return $this->toUser; // Feedback'in verildiği kişi
    }

    public function getData()
    {
        return [
            'feedbackId' => $this->feedback->id,
            'feedbackType' => $this->feedback->type,
            'toUsername' => $this->toUser ? $this->toUser->username : 'Unknown',
            'toUserId' => $this->feedback->to_user_id
        ];
    }

    public static function getType()
    {
        return 'feedbackApproved';
    }

    public static function getSubjectModel()
    {
        return User::class;
    }
}