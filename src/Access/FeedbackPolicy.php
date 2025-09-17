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
        if ($actor->hasPermission('huseyinfiliz-traderfeedback.moderate')) {
            return true;
        }
        
        // Users can edit their own feedback within 24 hours
        if ($feedback->from_user_id === $actor->id) {
            if (!$feedback->created_at) {
                return false;
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
        // Check if user has delete permission
        if ($actor->hasPermission('huseyinfiliz-traderfeedback.delete')) {
            return true;
        }
        
        // Users can delete their own feedback within 1 hour
        if ($feedback->from_user_id === $actor->id) {
            if (!$feedback->created_at) {
                return false;
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
        // Check if user has report permission
        if (!$actor->hasPermission('huseyinfiliz-traderfeedback.report')) {
            return false;
        }
        
        return true;
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
        return $actor->hasPermission('huseyinfiliz-traderfeedback.moderate');
    }
}