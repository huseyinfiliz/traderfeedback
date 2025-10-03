<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Foundation\ValidationException;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Services\StatsService;

class CreateFeedbackController extends AbstractCreateController
{
    public $serializer = FeedbackSerializer::class;
    public $include = ['fromUser', 'toUser'];
    
    protected $validator;
    protected $settings;
    
    public function __construct(
        FeedbackValidator $validator,
        SettingsRepositoryInterface $settings
    ) {
        $this->validator = $validator;
        $this->settings = $settings;
    }
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);
        
        // Check if discussion is required
        $requireDiscussion = $this->settings->get('huseyinfiliz.traderfeedback.requireDiscussion', false);
        if ($requireDiscussion && !Arr::get($data, 'discussion_id')) {
            throw new ValidationException([
                'discussion_id' => 'Discussion URL or ID is required for feedback.'
            ]);
        }
        
        // Check allow negative setting
        $allowNegative = $this->settings->get('huseyinfiliz.traderfeedback.allowNegative');
        if (($allowNegative === false || $allowNegative === "0" || $allowNegative === 0) && Arr::get($data, 'type') === Feedback::TYPE_NEGATIVE) {
            throw new ValidationException([
                'type' => 'Negative feedback is not allowed.'
            ]);
        }
        
        // Validate the request data
        $this->validator->assertValid($data);
        
        // Check if user is trying to give feedback to themselves
        $toUserId = (int) Arr::get($data, 'to_user_id');
        if ($actor->id == $toUserId) {
            throw new ValidationException([
                'to_user_id' => 'You cannot give feedback to yourself.'
            ]);
        }
        
        // Parse discussion ID from URL if needed
        $discussionId = Arr::get($data, 'discussion_id');
        if ($discussionId) {
            // Check if it's a URL
            if (preg_match('/\/d\/(\d+)/', $discussionId, $matches)) {
                $discussionId = (int) $matches[1];
            } else {
                $discussionId = (int) $discussionId;
            }
        }
        
        // Check one per discussion rule
        $onePerDiscussion = $this->settings->get('huseyinfiliz.traderfeedback.onePerDiscussion', true);
        if ($onePerDiscussion && $discussionId) {
            $existingFeedback = Feedback::where('from_user_id', $actor->id)
                ->where('to_user_id', $toUserId)
                ->where('discussion_id', $discussionId)
                ->exists();
                
            if ($existingFeedback) {
                throw new ValidationException([
                    'discussion_id' => 'You have already given feedback for this user in this discussion.'
                ]);
            }
        }
        
        // Validate discussion exists if provided
        if ($discussionId) {
            $discussionExists = \Flarum\Discussion\Discussion::find($discussionId);
            if (!$discussionExists) {
                throw new ValidationException([
                    'discussion_id' => 'The specified discussion does not exist.'
                ]);
            }
            
            // Check if discussion is deleted/hidden
            if ($discussionExists->hidden_at !== null) {
                throw new ValidationException([
                    'discussion_id' => 'The specified discussion is not available.'
                ]);
            }
        }
        
        // XSS Protection: Strip HTML tags from comment
        $rawComment = Arr::get($data, 'comment', '');
        $sanitizedComment = strip_tags($rawComment);
        
        // Create the feedback
        $feedback = new Feedback();
        $feedback->from_user_id = $actor->id;
        $feedback->to_user_id = $toUserId;
        $feedback->type = Arr::get($data, 'type');
        $feedback->comment = $sanitizedComment;
        $feedback->role = Arr::get($data, 'role');
        $feedback->discussion_id = $discussionId;
        $feedback->is_approved = !$this->settings->get('huseyinfiliz.traderfeedback.requireApproval', false);
        
        $feedback->save();
        
        // Load relationships
        $feedback->load(['fromUser', 'toUser']);
        
        // Update stats if feedback is approved
        if ($feedback->is_approved) {
            StatsService::updateUserStats($feedback->to_user_id);
        }
        
        // Fire the event - listener will handle the notification
        event(new FeedbackCreated($feedback, $actor));
        
        return $feedback;
    }
}