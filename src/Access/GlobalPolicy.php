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
        // Give feedback - varsayılan olarak tüm kullanıcılar
        if ($ability === 'huseyinfiliz-traderfeedback.give') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.give');
        }
        
        // Report feedback - varsayılan olarak tüm kullanıcılar
        if ($ability === 'huseyinfiliz-traderfeedback.report') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.report');
        }
        
        // Delete feedbacks - sadece yetkililer
        if ($ability === 'huseyinfiliz-traderfeedback.delete') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.delete');
        }
        
        // Moderate feedbacks - admin ve moderatörler
        if ($ability === 'huseyinfiliz-traderfeedback.moderate') {
            return $actor->hasPermission('huseyinfiliz-traderfeedback.moderate');
        }
        
        return null;
    }
}