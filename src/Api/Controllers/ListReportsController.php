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
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackReportSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['user', 'feedback', 'feedback.fromUser', 'feedback.toUser', 'resolvedBy'];

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        
        // Check permission
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        // Get only unresolved reports by default
        $query = FeedbackReport::where('resolved', false)
            ->with(['user', 'feedback', 'feedback.fromUser', 'feedback.toUser'])
            ->orderBy('created_at', 'desc');
        
        return $query->get();
    }
}