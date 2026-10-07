<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;

/**
 * @TODO: Remove this in favor of one of the API resource classes that were added.
 *      Or extend an existing API Resource to add this to.
 *      Or use a vanilla RequestHandlerInterface controller.
 *      @link https://docs.flarum.org/2.x/extend/api#endpoints
 */
class TraderStatsSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'trader-stats';

    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($stats)
    {
        return [
            'positive_count' => (int) $stats->positive_count,
            'negative_count' => (int) $stats->negative_count,
            'neutral_count'  => (int) $stats->neutral_count,
            'score'          => (float) $stats->score,
            'last_updated'   => $this->formatDate($stats->last_updated),
        ];
    }
}
