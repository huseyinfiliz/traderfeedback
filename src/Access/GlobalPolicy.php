<?php

namespace HuseyinFiliz\TraderFeedback\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class GlobalPolicy extends AbstractPolicy
{
    /**
     * @param User   $actor
     * @param string $ability
     *
     * @return bool|null
     */
    public function can(User $actor, string $ability)
    {
        // Respect the configured group permissions.
        if ($ability === 'huseyinfiliz-traderfeedback.give') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.give');
        }

        if ($ability === 'huseyinfiliz-traderfeedback.report') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.report');
        }

        if ($ability === 'huseyinfiliz-traderfeedback.delete') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.delete');
        }

        if ($ability === 'huseyinfiliz-traderfeedback.moderate') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.moderate');
        }

        return null;
    }
}
