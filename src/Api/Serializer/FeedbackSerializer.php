<?php

namespace HuseyinFiliz\TraderFeedback\Api\Serializer;

use Flarum\Discussion\Discussion;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;

class FeedbackSerializer
{
    /**
     * Serialize a single Feedback model into JSON:API resource array.
     */
    public static function feedback(Feedback $feedback, ?User $actor = null): array
    {
        $canEdit = $actor ? $actor->can('edit', $feedback) : false;
        $canDelete = $actor ? $actor->can('delete', $feedback) : false;
        $canReport = $actor ? $actor->can('report', $feedback) : false;
        $canModerate = $actor ? $actor->hasPermission('huseyinfiliz-traderfeedback.moderate') : false;

        $discussionExists = false;
        $canViewDiscussion = false;

        if ($feedback->discussion_id) {
            $discussion = $feedback->discussion;
            if ($discussion) {
                $discussionExists = true;
                $canViewDiscussion = $actor ? $actor->can('view', $discussion) : true;
            }
        }

        $createdAtIso = $feedback->created_at ? $feedback->created_at->toIso8601String() : null;
        $updatedAtIso = $feedback->updated_at ? $feedback->updated_at->toIso8601String() : null;

        return [
            'type'       => 'trader-feedbacks',
            'id'         => (string) $feedback->id,
            'attributes' => [
                'id'           => (int) $feedback->id,
                'type'         => $feedback->type,
                'comment'      => $feedback->comment,
                'role'         => $feedback->role,
                'isApproved'   => (bool) $feedback->is_approved,
                'fromUserId'   => (int) $feedback->from_user_id,
                'toUserId'     => (int) $feedback->to_user_id,
                'discussionId' => $feedback->discussion_id ? (int) $feedback->discussion_id : null,
                'approvedById' => $feedback->approved_by_id ? (int) $feedback->approved_by_id : null,
                'createdAt'    => $createdAtIso,
                'updatedAt'    => $updatedAtIso,
                // Duplicate fields for legacy / alternate frontend helper access
                'created_at'        => $createdAtIso,
                'from_user_id'      => (int) $feedback->from_user_id,
                'to_user_id'        => (int) $feedback->to_user_id,
                'discussion_id'     => $feedback->discussion_id ? (int) $feedback->discussion_id : null,
                'is_approved'       => (bool) $feedback->is_approved,
                'canEdit'           => $canEdit,
                'canDelete'         => $canDelete,
                'canReport'         => $canReport,
                'canApprove'        => $canModerate,
                'canModerate'       => $canModerate,
                'discussionExists'  => $discussionExists,
                'canViewDiscussion' => $canViewDiscussion,
            ],
            'relationships' => [
                'fromUser' => [
                    'data' => [
                        'type' => 'users',
                        'id'   => (string) $feedback->from_user_id,
                    ],
                ],
                'toUser' => [
                    'data' => [
                        'type' => 'users',
                        'id'   => (string) $feedback->to_user_id,
                    ],
                ],
                'discussion' => $feedback->discussion_id ? [
                    'data' => [
                        'type' => 'discussions',
                        'id'   => (string) $feedback->discussion_id,
                    ],
                ] : null,
            ],
        ];
    }

    /**
     * Resolve user slug via Flarum SlugManager with fallbacks.
     */
    public static function getUserSlug(User $user): string
    {
        try {
            return resolve(\Flarum\Http\SlugManager::class)->forResource(User::class)->toSlug($user) ?: ($user->username ?? (string) $user->id);
        } catch (\Throwable) {
            return $user->username ?: (string) $user->id;
        }
    }

    /**
     * Resolve discussion slug via Flarum SlugManager with fallbacks.
     */
    public static function getDiscussionSlug(Discussion $discussion): string
    {
        try {
            return resolve(\Flarum\Http\SlugManager::class)->forResource(Discussion::class)->toSlug($discussion) ?: (string) ($discussion->slug ?? $discussion->id);
        } catch (\Throwable) {
            return (string) ($discussion->slug ?: $discussion->id);
        }
    }

    /**
     * Serialize a User model into minimal JSON:API resource array.
     */
    public static function user(User $user): array
    {
        return [
            'type'       => 'users',
            'id'         => (string) $user->id,
            'attributes' => [
                'username'    => $user->username,
                'displayName' => $user->display_name,
                'avatarUrl'   => $user->avatar_url,
                'slug'        => self::getUserSlug($user),
            ],
        ];
    }

    /**
     * Serialize a Discussion model into minimal JSON:API resource array.
     */
    public static function discussion(Discussion $discussion): array
    {
        return [
            'type'       => 'discussions',
            'id'         => (string) $discussion->id,
            'attributes' => [
                'title' => $discussion->title,
                'slug'  => self::getDiscussionSlug($discussion),
            ],
        ];
    }

    /**
     * Serialize a FeedbackReport model into JSON:API resource array.
     */
    public static function report(FeedbackReport $report, ?User $actor = null): array
    {
        $createdAtIso = $report->created_at ? $report->created_at->toIso8601String() : null;
        $updatedAtIso = $report->updated_at ? $report->updated_at->toIso8601String() : null;

        return [
            'type'       => 'feedback-reports',
            'id'         => (string) $report->id,
            'attributes' => [
                'id'         => (int) $report->id,
                'reason'     => $report->reason,
                'resolved'   => (bool) $report->resolved,
                'created_at' => $createdAtIso,
                'updated_at' => $updatedAtIso,
                'createdAt'  => $createdAtIso,
                'updatedAt'  => $updatedAtIso,
            ],
            'relationships' => [
                'reporter' => [
                    'data' => [
                        'type' => 'users',
                        'id'   => (string) $report->user_id,
                    ],
                ],
                'user' => [
                    'data' => [
                        'type' => 'users',
                        'id'   => (string) $report->user_id,
                    ],
                ],
                'feedback' => [
                    'data' => [
                        'type' => 'trader-feedbacks',
                        'id'   => (string) $report->feedback_id,
                    ],
                ],
            ],
        ];
    }

    /**
     * Serialize a TraderStats model into JSON:API resource array.
     */
    public static function stats(TraderStats $stats): array
    {
        $lastUpdatedIso = $stats->last_updated ? $stats->last_updated->toIso8601String() : null;

        return [
            'type'       => 'trader-stats',
            'id'         => (string) $stats->id,
            'attributes' => [
                'positive_count' => (int) $stats->positive_count,
                'negative_count' => (int) $stats->negative_count,
                'neutral_count'  => (int) $stats->neutral_count,
                'positiveCount'  => (int) $stats->positive_count,
                'negativeCount'  => (int) $stats->negative_count,
                'neutralCount'   => (int) $stats->neutral_count,
                'score'          => (float) $stats->score,
                'last_updated'   => $lastUpdatedIso,
                'lastUpdated'    => $lastUpdatedIso,
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'users',
                        'id'   => (string) $stats->user_id,
                    ],
                ],
            ],
        ];
    }
}
