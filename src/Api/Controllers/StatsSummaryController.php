<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class StatsSummaryController extends AbstractListController
{
    public $serializer = 'HuseyinFiliz\TraderFeedback\Api\Serializers\StatsSummarySerializer';

    protected function data(ServerRequestInterface $request, Document $document)
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

        return collect([
            (object) [
                'id'       => 'summary',
                'total'    => $total,
                'positive' => $positive,
                'neutral'  => $neutral,
                'negative' => $negative,
            ],
        ]);
    }
}
