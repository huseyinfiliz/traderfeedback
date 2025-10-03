<?php
namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Carbon\Carbon;

class RejectFeedbackController extends AbstractShowController
{
    public $serializer = FeedbackSerializer::class;
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);
        
        // Store values before soft delete
        $toUserId = (int)$feedback->to_user_id;
        $fromUserId = (int)$feedback->from_user_id;
        $feedbackId = (int)$feedback->id;
        $feedbackType = $feedback->type;
        
        // Create notification before soft deleting feedback
        if ($fromUserId) {
            $now = Carbon::now();
            
            try {
                app('db')->table('notifications')->insert([
                    'user_id' => $fromUserId,
                    'from_user_id' => $toUserId,
                    'type' => 'feedbackRejected',
                    'subject_id' => $feedbackId,
                    'data' => json_encode([
                        'feedbackId' => $feedbackId,
                        'feedbackType' => $feedbackType
                    ], JSON_THROW_ON_ERROR),
                    'created_at' => $now,
                    'read_at' => null,
                    'is_deleted' => 0
                ]);
            } catch (\Exception $e) {
                // Silently fail - notification is not critical
            }
        }
        
        // Soft delete the feedback (sets deleted_at timestamp)
        $feedback->is_approved = false;
        $feedback->delete();
        
        // Update stats using service
        StatsService::updateUserStats($toUserId);
        
        return $feedback;
    }
}