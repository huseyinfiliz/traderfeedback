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
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use Carbon\Carbon;

class CreateFeedbackController extends AbstractCreateController
{
    public $serializer = FeedbackSerializer::class;
    public $include = ['fromUser', 'toUser'];
    
    protected $validator;
    protected $settings;
    protected $notifications;
    
    public function __construct(
        FeedbackValidator $validator,
        SettingsRepositoryInterface $settings,
        NotificationSyncer $notifications
    ) {
        $this->validator = $validator;
        $this->settings = $settings;
        $this->notifications = $notifications;
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
        
        // Check allow negative setting - FIXED
        $allowNegative = $this->settings->get('huseyinfiliz.traderfeedback.allowNegative');
        // Eğer ayar false veya "0" string ise negative'e izin verme
        if (($allowNegative === false || $allowNegative === "0" || $allowNegative === 0) && Arr::get($data, 'type') === 'negative') {
            throw new ValidationException([
                'type' => 'Negative feedback is not allowed.'
            ]);
        }
        
        // Validate the request data
        $this->validator->assertValid($data);
        
        // Check if user is trying to give feedback to themselves
        if ($actor->id == Arr::get($data, 'to_user_id')) {
            throw new ValidationException([
                'to_user_id' => 'You cannot give feedback to yourself.'
            ]);
        }
        
        // Check minimum requirements - FIXED
        $minDays = (int) $this->settings->get('huseyinfiliz.traderfeedback.minDays', 0);
        $minPosts = (int) $this->settings->get('huseyinfiliz.traderfeedback.minPosts', 0);
        
        if ($minDays > 0) {
            $daysSinceJoined = $actor->joined_at ? $actor->joined_at->diffInDays(Carbon::now()) : 0;
            if ($daysSinceJoined < $minDays) {
                // Doğrudan mesaj kullan, translation key kullanma
                throw new ValidationException([
                    'user' => "You must be a member for at least {$minDays} days to give feedback."
                ]);
            }
        }
        
        if ($minPosts > 0) {
            $userPostCount = $actor->comment_count ?? 0;
            if ($userPostCount < $minPosts) {
                // Doğrudan mesaj kullan, translation key kullanma
                throw new ValidationException([
                    'user' => "You must have at least {$minPosts} posts to give feedback."
                ]);
            }
        }
        
        // Check for duplicate feedback per discussion if enabled
        $discussionId = Arr::get($data, 'discussion_id');
        $onePerDiscussion = $this->settings->get('huseyinfiliz.traderfeedback.onePerDiscussion', true);
        
        if ($discussionId && $onePerDiscussion) {
            $existingFeedback = Feedback::where('from_user_id', $actor->id)
                ->where('to_user_id', Arr::get($data, 'to_user_id'))
                ->where('discussion_id', $discussionId);
                
            if ($existingFeedback->exists()) {
                throw new ValidationException([
                    'discussion_id' => 'You have already given feedback for this discussion.'
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
        }
        
        // Create the feedback
        $feedback = new Feedback();
        $feedback->from_user_id = $actor->id;
        $feedback->to_user_id = Arr::get($data, 'to_user_id');
        $feedback->type = Arr::get($data, 'type');
        $feedback->comment = Arr::get($data, 'comment');
        $feedback->role = Arr::get($data, 'role');
        $feedback->discussion_id = $discussionId;
        $feedback->is_approved = !$this->settings->get('huseyinfiliz.traderfeedback.requireApproval', false);
        
        $feedback->save();
        $feedback->load(['fromUser', 'toUser']);
        
        // Update user stats if feedback is approved
        if ($feedback->is_approved) {
            $this->updateUserStats($feedback->to_user_id);
        }
        
        // Send notification to the recipient if feedback is approved
        if ($feedback->is_approved && $feedback->toUser) {
            $this->notifications->sync(
                new NewFeedbackBlueprint($feedback),
                [$feedback->toUser]
            );
        }
        
        // Dispatch event
        event(new FeedbackCreated($feedback, $actor));
        
        return $feedback;
    }
    
    protected function updateUserStats($userId)
    {
        $stats = TraderStats::firstOrNew(['user_id' => $userId]);
        
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