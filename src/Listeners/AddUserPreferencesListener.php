<?php

namespace HuseyinFiliz\TraderFeedback\Listeners;

use Flarum\User\Event\Saving;
use Illuminate\Support\Arr;

class AddUserPreferencesListener
{
    /**
     * @param Saving $event
     */
    public function handle(Saving $event)
    {
        $user = $event->user;
        $data = $event->data;
        $actor = $event->actor;

        // Check if the user is updating their own preferences
        if ($actor->id !== $user->id) {
            return;
        }

        // Handle notification preferences for trader feedback
        if (Arr::has($data, 'attributes.preferences.notifyForNewFeedback')) {
            $user->setPreference(
                'notifyForNewFeedback',
                (bool) Arr::get($data, 'attributes.preferences.notifyForNewFeedback')
            );
        }

        if (Arr::has($data, 'attributes.preferences.notifyForFeedbackApproved')) {
            $user->setPreference(
                'notifyForFeedbackApproved',
                (bool) Arr::get($data, 'attributes.preferences.notifyForFeedbackApproved')
            );
        }

        if (Arr::has($data, 'attributes.preferences.notifyForFeedbackRejected')) {
            $user->setPreference(
                'notifyForFeedbackRejected',
                (bool) Arr::get($data, 'attributes.preferences.notifyForFeedbackRejected')
            );
        }
    }
}