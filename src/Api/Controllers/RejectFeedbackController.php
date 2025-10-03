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
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class RejectFeedbackController extends AbstractShowController
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
        
        // Notify feedback author about rejection BEFORE deleting
        if ($feedback->fromUser) {
            try {
                $blueprint = new FeedbackRejectedBlueprint($feedback);
                $this->notifications->sync($blueprint, [$feedback->fromUser]);
            } catch (\Exception $e) {
                app('log')->error('Failed to send rejection notification', [
                    'feedback_id' => $feedback->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Soft delete the feedback
        $feedback->is_approved = false;
        $feedback->delete();
        
        // Update stats
        StatsService::updateUserStats($feedback->to_user_id);
        
        return $feedback;
    }
}