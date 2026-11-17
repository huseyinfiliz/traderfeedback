<?php

namespace HuseyinFiliz\TraderFeedback\Access;

use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;

class ScopeForumVisibility
{
    public function __invoke(User $actor, Builder $query): void
    {
        if ($actor->cannot('viewForum')) {
            $query->whereRaw('FALSE');
        }
    }
}
