<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackReportSerializer;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Carbon\Carbon;

class ApproveReportController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackReportSerializer::class;

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        // Check permission
        $actor->assertCan('huseyinfiliz-traderfeedback.moderateFeedback');
        
        // Find the report
        $report = FeedbackReport::findOrFail($id);
        
        // Mark report as resolved
        $report->resolved = true;
        $report->resolved_by_id = $actor->id;
        $report->updated_at = Carbon::now();
        $report->save();
        
        // The feedback stays, report is just marked as reviewed and approved
        // Admin decided the feedback is legitimate
        
        return $report;
    }
}