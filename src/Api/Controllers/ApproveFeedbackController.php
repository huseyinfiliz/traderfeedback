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

class ApproveFeedbackController extends AbstractShowController
{
    public $serializer = FeedbackSerializer::class;
    
    protected $notifications;
    
    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');
        
        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);
        
        $wasApproved = $feedback->is_approved;
        
        // Approve feedback
        $feedback->is_approved = true;
        $feedback->approved_by_id = $actor->id;
        $feedback->save();
        
        // Update stats
        StatsService::updateUserStats($feedback->to_user_id);
        
        // Send notifications if newly approved
        if (!$wasApproved) {
            try {
                // 1. Notify feedback author about approval
                if ($feedback->fromUser) {
                    $approvalBlueprint = new FeedbackApprovedBlueprint($feedback);
                    $this->notifications->sync($approvalBlueprint, [$feedback->fromUser]);
                }
                
                // 2. Notify feedback recipient about new feedback
                if ($feedback->toUser) {
                    $newFeedbackBlueprint = new NewFeedbackBlueprint($feedback);
                    $this->notifications->sync($newFeedbackBlueprint, [$feedback->toUser]);
                }
            } catch (\Exception $e) {
                app('log')->error('Failed to send approval notifications', [
                    'feedback_id' => $feedback->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $feedback;
    }
}