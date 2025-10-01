<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackReportSerializer;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;

class ListReportsController extends AbstractListController
{
    public $serializer = FeedbackReportSerializer::class;
    
    // ÖNEMLİ: Nested relationships
    public $include = [
        'reporter',
        'feedback',
        'feedback.fromUser',
        'feedback.toUser'
    ];
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        
        // Permission check
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        // Query with eager loading - NULL check ekle
        return FeedbackReport::where('resolved', false)
            ->with([
                'reporter',
                'feedback' => function($query) {
                    // Sadece silinmemiş feedbackleri yükle
                    $query->whereNotNull('id');
                },
                'feedback.fromUser',
                'feedback.toUser'
            ])
            ->whereHas('feedback') // Sadece feedback'i olan report'ları getir
            ->orderBy('created_at', 'desc')
            ->get();
    }
}