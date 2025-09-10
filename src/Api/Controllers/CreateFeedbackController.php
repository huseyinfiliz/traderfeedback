<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
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
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackSerializer::class;
    
    /**
     * {@inheritdoc}
     */
    public $include = ['fromUser', 'toUser'];
    
    /**
     * @var FeedbackValidator
     */
    protected $validator;
    
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;
    
    /**
     * @var NotificationSyncer
     */
    protected $notifications;
    
    /**
     * @param FeedbackValidator $validator
     * @param SettingsRepositoryInterface $settings
     * @param NotificationSyncer $notifications
     */
    public function __construct(
        FeedbackValidator $validator,
        SettingsRepositoryInterface $settings,
        NotificationSyncer $notifications
    ) {
        $this->validator = $validator;
        $this->settings = $settings;
        $this->notifications = $notifications;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);
        
        // Validate the request data
        $this->validator->assertValid($data);
        
        // Check if user is trying to give feedback to themselves
        if ($actor->id == Arr::get($data, 'to_user_id')) {
            throw new ValidationException([
                'to_user_id' => 'You cannot give feedback to yourself'
            ]);
        }
        
        // Check minimum requirements (if enabled)
        $minDays = (int) $this->settings->get('huseyinfiliz.traderfeedback.minDays', 0);
        $minPosts = (int) $this->settings->get('huseyinfiliz.traderfeedback.minPosts', 0);
        
        if ($minDays > 0) {
            $daysSinceJoined = $actor->joined_at->diffInDays(Carbon::now());
            if ($daysSinceJoined < $minDays) {
                throw new ValidationException([
                    'user' => "You must be a member for at least {$minDays} days to give feedback."
                ]);
            }
        }
        
        if ($minPosts > 0 && $actor->comment_count < $minPosts) {
            throw new ValidationException([
                'user' => "You must have at least {$minPosts} posts to give feedback."
            ]);
        }
        
        // Check for duplicate feedback (optional - sadece discussion bazlı kontrol istiyorsanız)
        $existingFeedback = Feedback::where('from_user_id', $actor->id)
            ->where('to_user_id', Arr::get($data, 'to_user_id'));
            
        // Eğer discussion_id varsa, o discussion için feedback var mı kontrol et
        $discussionId = Arr::get($data, 'discussion_id');
        if ($discussionId) {
            $existingFeedback->where('discussion_id', $discussionId);
            if ($existingFeedback->exists()) {
                throw new ValidationException([
                    'discussion_id' => 'You have already given feedback for this discussion.'
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
        
        // Save the feedback (created_at ve updated_at otomatik doldurulacak)
        $feedback->save();
        
        // Feedback'i relationship'leriyle birlikte yükle
        $feedback->load(['fromUser', 'toUser']);
        
        // Update user stats if feedback is approved
        if ($feedback->is_approved) {
            $this->updateUserStats($feedback->to_user_id);
        }
        
        // Send notification to the recipient (only if approved)
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
    
    /**
     * Update user statistics after feedback creation
     * 
     * @param int $userId
     * @return void
     */
    protected function updateUserStats($userId)
    {
        $stats = TraderStats::firstOrNew(['user_id' => $userId]);
        
        // Count approved feedbacks by type
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
        
        // Calculate score percentage
        $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
        $stats->score = $total > 0 ? ($stats->positive_count / $total) * 100 : 0;
        
        // Update timestamp
        $stats->last_updated = Carbon::now();
        
        // Save stats
        $stats->save();
    }
}