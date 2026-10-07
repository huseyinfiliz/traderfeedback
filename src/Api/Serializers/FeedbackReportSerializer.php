<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;

/**
 * @TODO: Remove this in favor of one of the API resource classes that were added.
 *      Or extend an existing API Resource to add this to.
 *      Or use a vanilla RequestHandlerInterface controller.
 *      @link https://docs.flarum.org/2.x/extend/api#endpoints
 */
class FeedbackReportSerializer extends AbstractSerializer
{
    protected $type = 'feedback-reports';

    protected function getDefaultAttributes($report)
    {
        return [
            'id'         => $report->id,
            'reason'     => $report->reason,
            'resolved'   => (bool) $report->resolved,
            'created_at' => $this->formatDate($report->created_at),
            'updated_at' => $this->formatDate($report->updated_at),
        ];
    }

    /**
     * ✅ Reporter relationship.
     */
    protected function reporter($report)
    {
        return $this->hasOne($report, BasicUserSerializer::class);
    }

    /**
     * Get the reported feedback.
     */
    protected function feedback($report)
    {
        return $this->hasOne($report, FeedbackSerializer::class);
    }

    /**
     * Get the user who resolved the report.
     */
    protected function resolvedBy($report)
    {
        return $this->hasOne($report, BasicUserSerializer::class, 'resolvedBy');
    }
}
