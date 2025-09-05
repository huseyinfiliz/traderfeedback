<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;

class FeedbackReportSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'feedback-reports';

    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($report)
    {
        return [
            'id' => $report->id,
            'reason' => $report->reason,
            'resolved' => (bool) $report->resolved,
            'created_at' => $this->formatDate($report->created_at),
            'updated_at' => $this->formatDate($report->updated_at),
        ];
    }

    /**
     * Get the user who reported
     */
    protected function user($report)
    {
        return $this->hasOne($report, BasicUserSerializer::class);
    }

    /**
     * Get the reported feedback
     */
    protected function feedback($report)
    {
        return $this->hasOne($report, FeedbackSerializer::class);
    }

    /**
     * Get the user who resolved the report
     */
    protected function resolvedBy($report)
    {
        return $this->hasOne($report, BasicUserSerializer::class, 'resolvedBy');
    }
}