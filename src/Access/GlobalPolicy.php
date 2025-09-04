<?php

namespace HuseyinFiliz\TraderFeedback\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class GlobalPolicy extends AbstractPolicy
{
    /**
     * @param User $actor
     * @param string $ability
     * @return bool|null
     */
    public function can(User $actor, string $ability)
    {
        if ($ability === 'huseyinfiliz-traderfeedback.giveFeedback') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.giveFeedback');
        }
        
        if ($ability === 'huseyinfiliz-traderfeedback.moderateFeedback') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.moderateFeedback');
        }
        
        return null;
    }
}