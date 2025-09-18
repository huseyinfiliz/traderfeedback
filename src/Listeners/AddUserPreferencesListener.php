<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\User\Event\Saving;
use Illuminate\Support\Arr;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;

/**
 * Update user notification preferences when their profile is saved.
 *
 * Prior to this update the extension stored preferences under custom keys
 * (e.g. `notifyForFeedbackApproved`). Flarum 1.8 expects notification
 * preference keys in the format `notify_{type}_{channel}`; this listener
 * uses `User::getNotificationPreferenceKey()` to derive the correct key for
 * each blueprint type and updates the preference if it was included in the
 * incoming request.
 */
class AddUserPreferencesListener
{
    /**
     * Handle the Saving event for User models.
     */
    public function handle(Saving $event)
    {
        $user = $event->user;
        $data = $event->data;
        $actor = $event->actor;

        // Only allow users to modify their own preferences.
        if ($actor->id !== $user->id) {
            return;
        }

        // List of our notification blueprints that support alerts.
        $blueprints = [
            NewFeedbackBlueprint::class,
            FeedbackApprovedBlueprint::class,
            FeedbackRejectedBlueprint::class,
        ];

        foreach ($blueprints as $blueprint) {
            $type = $blueprint::getType();
            $key = User::getNotificationPreferenceKey($type, 'alert');
            $prefPath = "attributes.preferences." . $key;
            if (Arr::has($data, $prefPath)) {
                $user->setPreference(
                    $key,
                    (bool) Arr::get($data, $prefPath)
                );
            }
        }
    }
}