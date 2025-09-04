<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\TraderStatsSerializer;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;

class ShowTraderStatsController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = TraderStatsSerializer::class;

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $userId = Arr::get($request->getQueryParams(), 'id');
        
        $stats = TraderStats::where('user_id', $userId)->first();
        
        if (!$stats) {
            // Create empty stats if none exist
            $stats = new TraderStats();
            $stats->user_id = $userId;
            $stats->positive_count = 0;
            $stats->neutral_count = 0;
            $stats->negative_count = 0;
            $stats->score = 0;
            $stats->last_updated = now();
        }
        
        return $stats;
    }
}