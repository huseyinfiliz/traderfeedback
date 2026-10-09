<?php

namespace HuseyinFiliz\TraderFeedback\Notifications;

use Flarum\Database\AbstractModel;
use Flarum\Notification\AlertableInterface;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;

class FeedbackReportedBlueprint implements BlueprintInterface, AlertableInterface
{
    public function __construct(public FeedbackReport $report)
    {
    }

    public function getSubject(): ?AbstractModel
    {
        return $this->report;
    }

    public function getFromUser(): ?User
    {
        return $this->report->reporter ?? User::find($this->report->user_id);
    }

    public function getData(): mixed
    {
        $feedback = $this->report->feedback ?? Feedback::find($this->report->feedback_id);
        $toUser = $feedback?->toUser ?? ($feedback ? User::find($feedback->to_user_id) : null);
        $fromUser = $feedback?->fromUser ?? ($feedback ? User::find($feedback->from_user_id) : null);

        $toUserSlug = $toUser ? \HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer::getUserSlug($toUser) : null;

        return [
            'reportId'     => $this->report->id,
            'feedbackId'   => $this->report->feedback_id,
            'toUserId'     => $toUser?->id,
            'toUserSlug'   => $toUserSlug ?: ($toUser?->username ?? (string) $toUser?->id),
            'toUserName'   => $toUser?->display_name ?? $toUser?->username,
            'fromUserId'   => $fromUser?->id,
            'fromUserName' => $fromUser?->display_name ?? $fromUser?->username,
            'reason'       => mb_substr($this->report->reason, 0, 50).'...',
        ];
    }

    public static function getType(): string
    {
        return 'feedbackReported';
    }

    public static function getSubjectModel(): string
    {
        return FeedbackReport::class;
    }
}
