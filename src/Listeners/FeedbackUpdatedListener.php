<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use Flarum\Notification\NotificationSyncer;
use Carbon\Carbon;

/**
 * Listener for the FeedbackUpdated event.
 *
 * When a feedback entry is approved, this listener updates the recipient's
 * statistics and sends a `feedbackApproved` notification to the user who
 * originally submitted the feedback (the "from" user). The notification is
 * not sent if the moderator approves their own feedback.
 */
class FeedbackUpdatedListener
{
    /**
     * @var NotificationSyncer
     */
    protected $notifications;

    /**
     * Inject dependencies.
     */
    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Handle the event.
     */
    public function handle(FeedbackUpdated $event)
    {
        $feedback = $event->feedback;
        $actor = $event->actor;

        // Only act on approved feedback
        if (!$feedback->is_approved) {
            return;
        }

        // Load the required relationships once. `loadMissing` will only load
        // relations that are not already present on the model.
        $feedback->loadMissing(['fromUser', 'toUser']);

        // Update the stats of the user who received the feedback.
        $this->updateUserStats($feedback->to_user_id);

        // If the feedback has a "from" user and that user is not the approver,
        // send them a notification about their feedback being approved.
        if ($feedback->fromUser && $feedback->fromUser->id !== $actor->id) {
            $this->notifications->sync(
                new FeedbackApprovedBlueprint($feedback),
                [$feedback->fromUser]
            );
        }
    }

    /**
     * Recalculate the recipient's trader statistics.
     */
    protected function updateUserStats($userId)
    {
        $stats = TraderStats::firstOrNew(['user_id' => $userId]);

        // Count approved feedbacks for this user
        $feedbacks = \HuseyinFiliz\TraderFeedback\Models\Feedback::where('to_user_id', $userId)
            ->where('is_approved', true)
            ->get();

        $stats->positive_count = $feedbacks->where('type', 'positive')->count();
        $stats->negative_count = $feedbacks->where('type', 'negative')->count();
        $stats->neutral_count = $feedbacks->where('type', 'neutral')->count();

        // Calculate score
        $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
        $stats->score = $total > 0 ? ($stats->positive_count / $total) * 100 : 0;
        $stats->last_updated = Carbon::now();

        $stats->save();
    }
}