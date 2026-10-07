<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StatsSummaryController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('huseyinfiliz-traderfeedback.moderate');

        $counts = Feedback::where('is_approved', true)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $positive = (int) ($counts['positive'] ?? 0);
        $neutral = (int) ($counts['neutral'] ?? 0);
        $negative = (int) ($counts['negative'] ?? 0);
        $total = $positive + $neutral + $negative;

        $attributes = [
            'total' => $total,
            'positive' => $positive,
            'neutral' => $neutral,
            'negative' => $negative,
        ];

        return new JsonResponse([
            'data' => [
                'type' => 'trader-stats-summary',
                'id' => 'summary',
                'attributes' => $attributes,
            ],
            'total' => $total,
            'positive' => $positive,
            'neutral' => $neutral,
            'negative' => $negative,
        ]);
    }
}
