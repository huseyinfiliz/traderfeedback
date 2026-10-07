<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ShowTraderStatsController implements RequestHandlerInterface
{
    public function __construct(protected Cache $cache)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $userId = (int) ($routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id'));

        $cacheKey = "trader_stats_{$userId}";
        $stats = $this->cache->get($cacheKey);

        if (!$stats) {
            $stats = StatsService::updateUserStats($userId);
            $this->cache->put($cacheKey, $stats, 3600);
        }

        return new JsonResponse([
            'data' => FeedbackSerializer::stats($stats),
        ]);
    }
}
