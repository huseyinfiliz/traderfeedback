<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;

class StatsSummarySerializer extends AbstractSerializer
{
    protected $type = 'trader-stats-summary';

    protected function getDefaultAttributes($stats)
    {
        return [
            'total' => (int) $stats->total,
            'positive' => (int) $stats->positive,
            'neutral' => (int) $stats->neutral,
            'negative' => (int) $stats->negative,
        ];
    }
}