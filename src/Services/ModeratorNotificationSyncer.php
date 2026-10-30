<?php

namespace HuseyinFiliz\TraderFeedback\Services;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\Notification;
use Flarum\Notification\NotificationSyncer;

class ModeratorNotificationSyncer extends NotificationSyncer
{
    public function __construct(protected ModeratorFinder $moderators)
    {
    }

    public function syncModerators(BlueprintInterface $blueprint, ?int $excludeUserId = null): void
    {
        // Reconcile the full audience once; syncing each batch through sync()
        // would delete notifications belonging to every preceding batch.
        $this->delete($blueprint);

        foreach ($this->moderators->batches($excludeUserId) as $users) {
            $existing = Notification::matchingBlueprint($blueprint)
                ->whereIn('user_id', array_map(fn ($user) => $user->id, $users))
                ->get()
                ->keyBy('user_id');
            $toUndelete = [];
            $newRecipients = [];

            foreach ($users as $user) {
                if ($notification = $existing->get($user->id)) {
                    $toUndelete[] = $notification->id;
                } elseif (!static::$onePerUser || !in_array($user->id, static::$sentTo)) {
                    $newRecipients[] = $user;
                    if (static::$onePerUser) {
                        static::$sentTo[] = $user->id;
                    }
                }
            }

            if ($toUndelete) {
                $this->setDeleted($toUndelete, false);
            }

            foreach (static::$beforeSendingCallbacks as $callback) {
                $newRecipients = $callback($blueprint, $newRecipients);
            }

            foreach (static::getNotificationDrivers() as $driver) {
                $driver->send($blueprint, $newRecipients);
            }
        }
    }
}
