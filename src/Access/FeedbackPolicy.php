<?php

namespace HuseyinFiliz\TraderFeedback\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Carbon\Carbon;

class FeedbackPolicy extends AbstractPolicy
{
    /**
     * @param User $actor
     * @param Feedback $feedback
     * @return bool|null
     */
    public function edit(User $actor, Feedback $feedback)
    {
        // Moderators can always edit
        if ($actor->can('huseyinfiliz-traderfeedback.moderateFeedback')) {
            return true;
        }
        
        // Users can edit their own feedback within 24 hours
        if ($feedback->from_user_id === $actor->id) {
            // Check if created_at exists
            if (!$feedback->created_at) {
                return false; // If no creation date, don't allow edit
            }
            
            $hoursSinceCreated = $feedback->created_at->diffInHours(Carbon::now());
            return $hoursSinceCreated <= 24;
        }

        return false;
    }

    /**
     * @param User $actor
     * @param Feedback $feedback
     * @return bool|null
     */
    public function delete(User $actor, Feedback $feedback)
    {
        // Moderators can always delete
        if ($actor->can('huseyinfiliz-traderfeedback.moderateFeedback')) {
            return true;
        }
        
        // Users can delete their own feedback within 1 hour
        if ($feedback->from_user_id === $actor->id) {
            // Check if created_at exists
            if (!$feedback->created_at) {
                return false; // If no creation date, don't allow delete
            }
            
            $minutesSinceCreated = $feedback->created_at->diffInMinutes(Carbon::now());
            return $minutesSinceCreated <= 60;
        }

        return false;
    }

    /**
     * @param User $actor
     * @param Feedback $feedback
     * @return bool|null
     */
    public function report(User $actor, Feedback $feedback)
    {
        // Users cannot report their own feedback
        if ($feedback->from_user_id === $actor->id) {
            return false;
        }

        // Users cannot report feedback they received (conflict of interest)
        if ($feedback->to_user_id === $actor->id) {
            return false;
        }

        // Any other logged-in user can report
        return $actor->exists;
    }

    /**
     * @param User $actor
     * @param Feedback $feedback
     * @return bool|null
     */
    public function view(User $actor, Feedback $feedback)
    {
        // Everyone can view approved feedback
        if ($feedback->is_approved) {
            return true;
        }
        
        // User can view their own unapproved feedback
        if ($feedback->from_user_id === $actor->id || $feedback->to_user_id === $actor->id) {
            return true;
        }
        
        // Moderators can view all feedback
        return $actor->can('huseyinfiliz-traderfeedback.moderateFeedback');
    }
}