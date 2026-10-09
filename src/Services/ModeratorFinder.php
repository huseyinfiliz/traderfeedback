<?php

namespace HuseyinFiliz\TraderFeedback\Services;

use Flarum\Group\Group;
use Flarum\Group\Permission;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Collection;

class ModeratorFinder
{
    /**
     * Get all users who can moderate trader feedback, excluding a specific user ID.
     *
     * @param int|null $excludeUserId
     *
     * @return Collection<int, User>
     */
    public static function getModerators(?int $excludeUserId = null): Collection
    {
        $groupPermissionGroupIds = Permission::where('permission', 'huseyinfiliz-traderfeedback.moderate')
            ->pluck('group_id')
            ->toArray();
        $groupPermissionGroupIds[] = Group::ADMINISTRATOR_ID;

        $query = User::query();

        if (!in_array(Group::MEMBER_ID, $groupPermissionGroupIds)) {
            $query->whereHas('groups', function ($q) use ($groupPermissionGroupIds) {
                $q->whereIn('id', $groupPermissionGroupIds);
            });
        }

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->get()->filter(function (User $user) {
            return $user->can('huseyinfiliz-traderfeedback.moderate');
        });
    }
}
