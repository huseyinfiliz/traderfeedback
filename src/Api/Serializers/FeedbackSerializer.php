<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Tobscure\JsonApi\Relationship;

class FeedbackSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'trader-feedbacks';

    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($feedback)
    {
        return [
            'id' => $feedback->id,
            'type' => $feedback->type,
            'comment' => $feedback->comment,
            'role' => $feedback->role,
            'transaction_id' => $feedback->transaction_id,
            'is_approved' => (bool) $feedback->is_approved,
            'created_at' => $this->formatDate($feedback->created_at),
            'updated_at' => $this->formatDate($feedback->updated_at),
            'canEdit' => $this->actor->can('edit', $feedback),
            'canDelete' => $this->actor->can('delete', $feedback)
        ];
    }

    /**
     * @param Feedback $feedback
     * @return Relationship
     */
    protected function fromUser($feedback)
    {
        return $this->hasOne($feedback, BasicUserSerializer::class, 'fromUser');
    }

    /**
     * @param Feedback $feedback
     * @return Relationship
     */
    protected function toUser($feedback)
    {
        return $this->hasOne($feedback, BasicUserSerializer::class, 'toUser');
    }
}