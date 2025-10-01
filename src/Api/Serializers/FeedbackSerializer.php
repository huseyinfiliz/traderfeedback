<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Api\Serializer\BasicDiscussionSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use InvalidArgumentException;

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
        if (!($feedback instanceof Feedback)) {
            throw new InvalidArgumentException(
                get_class($this) . ' can only serialize instances of ' . Feedback::class
            );
        }

        $attributes = [
            'id' => (int) $feedback->id,
            'type' => $feedback->type,
            'comment' => $feedback->comment,
            'role' => $feedback->role,
            'isApproved' => (bool) $feedback->is_approved,
            'fromUserId' => (int) $feedback->from_user_id,
            'toUserId' => (int) $feedback->to_user_id,
            'discussionId' => $feedback->discussion_id ? (int) $feedback->discussion_id : null,
            'approvedById' => $feedback->approved_by_id ? (int) $feedback->approved_by_id : null,
            // Tarih formatlaması
            'createdAt' => $feedback->created_at ? $feedback->created_at->toIso8601String() : null,
            'updatedAt' => $feedback->updated_at ? $feedback->updated_at->toIso8601String() : null,
            // Frontend için ek bilgiler
            'created_at' => $feedback->created_at ? $feedback->created_at->toIso8601String() : null,
            'from_user_id' => (int) $feedback->from_user_id,
        ];
        
        // Permission checks
        if ($this->actor) {
            // Edit permission
            $attributes['canEdit'] = $this->actor->can('edit', $feedback);
            
            // Delete permission - check both model policy and global permission
            $attributes['canDelete'] = $this->actor->can('delete', $feedback);
            
            // Report permission - check if user can report this specific feedback
            $attributes['canReport'] = $this->actor->can('report', $feedback);
            
            // Moderate permission
            $attributes['canApprove'] = $this->actor->hasPermission('huseyinfiliz-traderfeedback.moderate');
            $attributes['canModerate'] = $this->actor->hasPermission('huseyinfiliz-traderfeedback.moderate');
            
            // Discussion visibility check - kullanıcı bazlı kontrol
            if ($feedback->discussion_id) {
                $discussion = \Flarum\Discussion\Discussion::find($feedback->discussion_id);
                
                if ($discussion) {
                    // Tartışma var ve kullanıcı görebiliyor mu?
                    $attributes['discussionExists'] = true;
                    $attributes['canViewDiscussion'] = $this->actor->can('view', $discussion);
                } else {
                    // Tartışma silinmiş
                    $attributes['discussionExists'] = false;
                    $attributes['canViewDiscussion'] = false;
                }
            } else {
                // Hiç tartışma yok
                $attributes['discussionExists'] = false;
                $attributes['canViewDiscussion'] = false;
            }
        } else {
            // Guest kullanıcı
            $attributes['canEdit'] = false;
            $attributes['canDelete'] = false;
            $attributes['canReport'] = false;
            $attributes['canApprove'] = false;
            $attributes['canModerate'] = false;
            $attributes['discussionExists'] = false;
            $attributes['canViewDiscussion'] = false;
        }
        
        return $attributes;
    }

    /**
     * @param Feedback $feedback
     * @return \Tobscure\JsonApi\Relationship
     */
    protected function fromUser($feedback)
    {
        return $this->hasOne($feedback, BasicUserSerializer::class, 'fromUser');
    }

    /**
     * @param Feedback $feedback
     * @return \Tobscure\JsonApi\Relationship
     */
    protected function toUser($feedback)
    {
        return $this->hasOne($feedback, BasicUserSerializer::class, 'toUser');
    }
    
    /**
     * @param Feedback $feedback
     * @return \Tobscure\JsonApi\Relationship|null
     */
    protected function approvedBy($feedback)
    {
        if ($feedback->approved_by_id) {
            return $this->hasOne($feedback, BasicUserSerializer::class, 'approvedBy');
        }
        return null;
    }

    /**
     * @param Feedback $feedback
     * @return \Tobscure\JsonApi\Relationship|null
     */
    protected function discussion($feedback)
    {
        if ($feedback->discussion_id) {
            return $this->hasOne($feedback, BasicDiscussionSerializer::class, 'discussion');
        }
        return null;
    }
}