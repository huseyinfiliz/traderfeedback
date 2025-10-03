<?php
namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Carbon\Carbon;

class ApproveFeedbackController extends AbstractShowController
{
    public $serializer = FeedbackSerializer::class;
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        $feedback = Feedback::findOrFail($id);
        
        // Check if already approved
        $wasApproved = $feedback->is_approved;
        
        // Approve feedback
        $feedback->is_approved = true;
        $feedback->approved_by_id = $actor->id;
        $feedback->save();
        
        // Reload with relationships
        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);
        
        // Update stats using service
        StatsService::updateUserStats($feedback->to_user_id);
        
        // Send notifications if newly approved
        if (!$wasApproved) {
            // 1. Notify feedback author about approval
            if ($feedback->fromUser) {
                $now = Carbon::now();
                
                try {
                    app('db')->table('notifications')->insert([
                        'user_id' => (int)$feedback->fromUser->id,
                        'from_user_id' => (int)$feedback->to_user_id,
                        'type' => 'feedbackApproved',
                        'subject_id' => (int)$feedback->id,
                        'data' => json_encode([
                            'feedbackId' => (int)$feedback->id,
                            'feedbackType' => $feedback->type
                        ], JSON_THROW_ON_ERROR),
                        'created_at' => $now,
                        'read_at' => null,
                        'is_deleted' => 0
                    ]);
                } catch (\Exception $e) {
                    // Silently fail - notification is not critical
                }
            }
            
            // 2. Notify feedback recipient about new feedback
            if ($feedback->toUser) {
                try {
                    $notifications = app(NotificationSyncer::class);
                    $newFeedbackBlueprint = new NewFeedbackBlueprint($feedback);
                    $notifications->sync($newFeedbackBlueprint, [$feedback->toUser]);
                } catch (\Exception $e) {
                    // Silently fail - notification is not critical
                }
            }
        }
        
        return $feedback;
    }
}