<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;

class MinimalUserSerializer extends AbstractSerializer
{
    protected $type = 'users';

    /**
     * Sadece 3 alan: username, displayName, avatarUrl.
     */
    protected function getDefaultAttributes($user)
    {
        return [
            'username'    => $user->username,
            'displayName' => $user->display_name ?? $user->username,
            'avatarUrl'   => $user->avatar_url,
        ];
    }

    public function getId($user)
    {
        return $user->id;
    }
}
