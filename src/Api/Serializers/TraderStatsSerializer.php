<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;

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
            'neutral_count' => (int) $stats->neutral_count,
            'score' => (float) $stats->score,
            'last_updated' =>  $stats->neutral_count,
            'score' => (float) $stats->score,
            'last_updated' => $this->formatDate($stats->last_updated)
        ];
    }
}