<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;

/**
 * @TODO: Remove this in favor of one of the API resource classes that were added.
 *      Or extend an existing API Resource to add this to.
 *      Or use a vanilla RequestHandlerInterface controller.
 *      @link https://docs.flarum.org/2.x/extend/api#endpoints
 */
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
