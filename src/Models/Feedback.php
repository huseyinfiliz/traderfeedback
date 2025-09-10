<?php

namespace HuseyinFiliz\TraderFeedback\Models;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\User\User;
use Flarum\Discussion\Discussion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property string $type
 * @property string $role
 * @property string $comment
 * @property int|null $discussion_id
 * @property bool $is_approved
 * @property int|null $approved_by_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $fromUser
 * @property User $toUser
 * @property User|null $approvedBy
 * @property Discussion|null $discussion
 */
class Feedback extends AbstractModel
{
    use ScopeVisibilityTrait;

    /**
     * {@inheritdoc}
     */
    protected $table = 'tfb_feedbacks';

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'from_user_id' => 'integer',
        'to_user_id' => 'integer',
        'discussion_id' => 'integer',
        'approved_by_id' => 'integer',
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'type',
        'role',
        'comment',
        'discussion_id',
        'is_approved',
        'approved_by_id'
    ];

    /**
     * Feedback types
     */
    const TYPE_POSITIVE = 'positive';
    const TYPE_NEUTRAL = 'neutral';
    const TYPE_NEGATIVE = 'negative';

    /**
     * Feedback roles
     */
    const ROLE_BUYER = 'buyer';
    const ROLE_SELLER = 'seller';
    const ROLE_TRADER = 'trader';

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($feedback) {
            // Set timestamps if not set
            if (!$feedback->created_at) {
                $feedback->created_at = Carbon::now();
            }
            if (!$feedback->updated_at) {
                $feedback->updated_at = Carbon::now();
            }
        });

        static::updating(function ($feedback) {
            // Update the updated_at timestamp
            $feedback->updated_at = Carbon::now();
        });
    }

    /**
     * Get the user who gave the feedback.
     *
     * @return BelongsTo
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who received the feedback.
     *
     * @return BelongsTo
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Get the user who approved the feedback.
     *
     * @return BelongsTo
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    /**
     * Get the discussion associated with the feedback.
     *
     * @return BelongsTo
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class, 'discussion_id');
    }

    /**
     * Get all reports for this feedback.
     */
    public function reports()
    {
        return $this->hasMany(FeedbackReport::class, 'feedback_id');
    }

    /**
     * Scope a query to only include approved feedbacks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include pending feedbacks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope a query to only include feedbacks for a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('to_user_id', $userId);
    }

    /**
     * Scope a query to only include feedbacks from a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('from_user_id', $userId);
    }

    /**
     * Scope a query to only include feedbacks of a specific type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include feedbacks with a specific role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check if feedback is positive
     *
     * @return bool
     */
    public function isPositive(): bool
    {
        return $this->type === self::TYPE_POSITIVE;
    }

    /**
     * Check if feedback is negative
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->type === self::TYPE_NEGATIVE;
    }

    /**
     * Check if feedback is neutral
     *
     * @return bool
     */
    public function isNeutral(): bool
    {
        return $this->type === self::TYPE_NEUTRAL;
    }
}