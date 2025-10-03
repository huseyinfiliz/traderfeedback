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
use HuseyinFiliz\TraderFeedback\Events\FeedbackDeleted;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Carbon\Carbon;

class RejectReportController extends AbstractShowController
{
    public $serializer = FeedbackReportSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        $report = FeedbackReport::findOrFail($id);
        $feedback = $report->feedback;
        
        // Mark report as resolved
        $report->resolved = true;
        $report->resolved_by_id = $actor->id;
        $report->updated_at = Carbon::now();
        $report->save();
        
        // Delete the reported feedback (admin agreed it should be removed)
        if ($feedback) {
            $toUserId = $feedback->to_user_id;
            
            // Dispatch event before deletion
            event(new FeedbackDeleted($feedback, $actor));
            
            // Delete the feedback
            $feedback->delete();
            
            // Update stats using service
            StatsService::updateUserStats($toUserId);
        }
        
        return $report;
    }
}