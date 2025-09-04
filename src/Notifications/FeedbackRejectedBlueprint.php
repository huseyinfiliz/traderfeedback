<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackRejectedBlueprint implements BlueprintInterface
{
    /**
     * @var Feedback
     */
    public $feedback;

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
        return $this->feedback;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromUser()
    {
        return $this->feedback->approvedBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [];
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
        return Feedback::class;
    }
}