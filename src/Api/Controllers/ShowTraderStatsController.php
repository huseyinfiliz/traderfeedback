<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Api\Serializers\TraderStatsSerializer;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

/**
 * @TODO: Remove this in favor of one of the API resource classes that were added.
 *      Or extend an existing API Resource to add this to.
 *      Or use a vanilla RequestHandlerInterface controller.
 *      @link https://docs.flarum.org/2.x/extend/api#endpoints
 */
class ShowTraderStatsController extends AbstractShowController
{
    public $serializer = TraderStatsSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $userId = (int) Arr::get($request->getQueryParams(), 'id');

        // Cache key for this user's stats
        $cacheKey = "trader_stats_{$userId}";
        $cache = app('cache.store');

        // Try to get from cache (60 minutes TTL)
        $stats = $cache->get($cacheKey);

        if (!$stats) {
            // Not in cache, calculate and store
            $stats = StatsService::updateUserStats($userId);
            $cache->put($cacheKey, $stats, 3600); // 3600 seconds = 1 hour
        }

        return $stats;
    }
}
