<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;

/**
 * @TODO: Remove this in favor of one of the API resource classes that were added.
 *      Or extend an existing API Resource to add this to.
 *      Or use a vanilla RequestHandlerInterface controller.
 *      @link https://docs.flarum.org/2.x/extend/api#endpoints
 */
class StatsSummarySerializer extends AbstractSerializer
{
    protected $type = 'trader-stats-summary';

    protected function getDefaultAttributes($stats)
    {
        return [
            'total'    => (int) $stats->total,
            'positive' => (int) $stats->positive,
            'neutral'  => (int) $stats->neutral,
            'negative' => (int) $stats->negative,
        ];
    }
}
