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
use Carbon\Carbon;

class RejectReportController extends AbstractShowController
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
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        // Find the report
        $report = FeedbackReport::findOrFail($id);
        
        // Get the feedback
        $feedback = $report->feedback;
        
        // Mark report as resolved
        $report->resolved = true;
        $report->resolved_by_id = $actor->id;
        $report->updated_at = Carbon::now();
        $report->save();
        
        // Delete the reported feedback (admin agreed it should be removed)
        if ($feedback) {
            // Dispatch event before deletion
            event(new FeedbackDeleted($feedback, $actor));
            
            // Delete the feedback
            $feedback->delete();
            
            // Update stats for the user who received the feedback
            $this->updateUserStats($feedback->to_user_id);
        }
        
        return $report;
    }
    
    /**
     * Update user statistics after feedback deletion
     */
    protected function updateUserStats($userId)
    {
        $stats = \HuseyinFiliz\TraderFeedback\Models\TraderStats::where('user_id', $userId)->first();
        
        if ($stats) {
            // Recalculate stats
            $stats->positive_count = Feedback::where('to_user_id', $userId)
                ->where('type', 'positive')
                ->where('is_approved', true)
                ->count();
                
            $stats->neutral_count = Feedback::where('to_user_id', $userId)
                ->where('type', 'neutral')
                ->where('is_approved', true)
                ->count();
                
            $stats->negative_count = Feedback::where('to_user_id', $userId)
                ->where('type', 'negative')
                ->where('is_approved', true)
                ->count();
            
            $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
            $stats->score = $total > 0 ? ($stats->positive_count / $total) * 100 : 0;
            $stats->last_updated = Carbon::now();
            $stats->save();
        }
    }
}