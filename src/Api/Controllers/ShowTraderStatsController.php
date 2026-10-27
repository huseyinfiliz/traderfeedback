<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ShowTraderStatsController implements RequestHandlerInterface
{
    public function __construct(protected StatsService $stats)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $userId = (int) ($routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id'));

        $stats = $this->stats->forUser($userId);

        return new JsonResponse([
            'data' => FeedbackSerializer::stats($stats),
        ]);
    }
}
