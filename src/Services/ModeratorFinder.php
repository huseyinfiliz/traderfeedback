<?php

namespace HuseyinFiliz\TraderFeedback\Services;

use Flarum\Group\Group;
use Flarum\Group\Permission;
use Flarum\User\User;
use Generator;

class ModeratorFinder
{
    /**
     * Yield bounded batches of users who can moderate trader feedback.
     *
     * @param int|null $excludeUserId
     *
     * @return Generator<int, array<int, User>>
     */
    public function batches(?int $excludeUserId = null): Generator
    {
        $groupPermissionGroupIds = Permission::where('permission', 'huseyinfiliz-traderfeedback.moderate')
            ->pluck('group_id')
            ->toArray();
        $groupPermissionGroupIds[] = Group::ADMINISTRATOR_ID;

        $query = User::query();

        if (!in_array(Group::MEMBER_ID, $groupPermissionGroupIds) && !in_array(Group::GUEST_ID, $groupPermissionGroupIds)) {
            $query->whereHas('groups', function ($q) use ($groupPermissionGroupIds) {
                $q->whereIn('id', $groupPermissionGroupIds);
            });
        }

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        $batch = [];
        foreach ($query->with('groups')->lazyById(200) as $user) {
            if (!$user->can('huseyinfiliz-traderfeedback.moderate')) {
                continue;
            }

            $batch[] = $user;
            if (count($batch) === 200) {
                yield $batch;
                $batch = [];
            }
        }

        if ($batch) {
            yield $batch;
        }
    }
}
