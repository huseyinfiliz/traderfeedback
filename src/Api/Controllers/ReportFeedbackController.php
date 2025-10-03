<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\Foundation\ValidationException;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackReportSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use Carbon\Carbon;

class ReportFeedbackController extends AbstractCreateController
{
    public $serializer = FeedbackReportSerializer::class;
    public $include = ['reporter', 'feedback', 'feedback.fromUser', 'feedback.toUser'];

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data', []);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $feedback = Feedback::findOrFail($id);
        
        // Check if user can report this feedback
        $actor->assertCan('report', $feedback);
        
        // Check if already reported by this user
        $existingReport = FeedbackReport::where('feedback_id', $feedback->id)
            ->where('user_id', $actor->id)
            ->where('resolved', false)
            ->first();
            
        if ($existingReport) {
            throw new ValidationException([
                'feedback' => app('translator')->trans('huseyinfiliz-traderfeedback.api.validation.already_reported')
            ]);
        }
        
        // Rate Limit Check: Max 1 report per minute
        $recentReport = FeedbackReport::where('user_id', $actor->id)
            ->where('created_at', '>', Carbon::now()->subMinute())
            ->exists();
        
        if ($recentReport) {
            throw new ValidationException([
                'rate_limit' => app('translator')->trans(
                    'huseyinfiliz-traderfeedback.api.validation.rate_limit_report',
                    ['seconds' => 60]
                )
            ]);
        }
        
        // Create the report
        $report = new FeedbackReport();
        $report->user_id = $actor->id;
        $report->feedback_id = $feedback->id;
        $report->reason = Arr::get($data, 'attributes.reason', 'No reason provided');
        $report->resolved = false;
        $report->save();
        
        // Load relationships
        $report->load(['reporter', 'feedback', 'feedback.fromUser', 'feedback.toUser']);
        
        return $report;
    }
}