<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class StatsSummaryController extends AbstractListController
{
    public $serializer = 'HuseyinFiliz\TraderFeedback\Api\Serializers\StatsSummarySerializer';
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        $total = Feedback::where('is_approved', true)->count();
        $positive = Feedback::where('is_approved', true)->where('type', 'positive')->count();
        $neutral = Feedback::where('is_approved', true)->where('type', 'neutral')->count();
        $negative = Feedback::where('is_approved', true)->where('type', 'negative')->count();
        
        // Collection döndür (serializer için)
        return collect([
            (object)[
                'id' => 'summary',
                'total' => $total,
                'positive' => $positive,
                'neutral' => $neutral,
                'negative' => $negative,
            ]
        ]);
    }
}