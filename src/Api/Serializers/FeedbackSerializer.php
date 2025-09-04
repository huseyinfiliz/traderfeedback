<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackSerializer extends AbstractSerializer
{
    protected $type = 'trader-feedbacks';

    protected function getDefaultAttributes($feedback)
    {
        return [
            'id' => $feedback->id,
            'type' => $feedback->type,
            'comment' => $feedback->comment,
            'role' => $feedback->role,
            'is_approved' => (bool) $feedback->is_approved,
            'from_user_id' => $feedback->from_user_id,
            'to_user_id' => $feedback->to_user_id,
            'created_at' => $this->formatDate($feedback->created_at),
            'updated_at' => $this->formatDate($feedback->updated_at),
            'canEdit' => $this->actor->can('edit', $feedback),
            'canDelete' => $this->actor->can('delete', $feedback)
        ];
    }

    protected function fromUser($feedback)
    {
        return $this->hasOne($feedback, BasicUserSerializer::class, 'fromUser');
    }

    protected function toUser($feedback)
    {
        return $this->hasOne($feedback, BasicUserSerializer::class, 'toUser');
    }
}