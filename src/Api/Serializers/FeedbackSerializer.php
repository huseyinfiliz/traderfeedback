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
        $attributes = [
            'id' => $feedback->id,
            'type' => $feedback->type,
            'comment' => $feedback->comment,
            'role' => $feedback->role,
            'is_approved' => (bool) $feedback->is_approved,
            'from_user_id' => $feedback->from_user_id,
            'to_user_id' => $feedback->to_user_id,
            'created_at' => $this->formatDate($feedback->created_at),
            'updated_at' => $this->formatDate($feedback->updated_at),
        ];
        
        // Only check permissions if actor exists
        if ($this->actor) {
            try {
                $attributes['canEdit'] = $this->actor->can('edit', $feedback);
            } catch (\Exception $e) {
                $attributes['canEdit'] = false;
            }
            
            try {
                $attributes['canDelete'] = $this->actor->can('delete', $feedback);
            } catch (\Exception $e) {
                $attributes['canDelete'] = false;
            }
            
            try {
                $attributes['canReport'] = $this->actor->can('report', $feedback);
            } catch (\Exception $e) {
                $attributes['canReport'] = false;
            }
        } else {
            $attributes['canEdit'] = false;
            $attributes['canDelete'] = false;
            $attributes['canReport'] = false;
        }
        
        return $attributes;
    }

    protected function fromUser($feedback)
    {
        return $this->hasOne($feedback, BasicUserSerializer::class, 'fromUser');
    }

    protected function toUser($feedback)
    {
        return $this->hasOne($feedback, BasicUserSerializer::class, 'toUser');
    }
    
    protected function approvedBy($feedback)
    {
        if ($feedback->approved_by_id) {
            return $this->hasOne($feedback, BasicUserSerializer::class, 'approvedBy');
        }
        return null;
    }
}