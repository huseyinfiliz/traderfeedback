<?php

namespace HuseyinFiliz\TraderFeedback\Models;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;

/**
 * @property int           $id
 * @property int           $feedback_id
 * @property int           $user_id
 * @property string        $reason
 * @property bool          $resolved
 * @property int|null      $resolved_by_id
 * @property Carbon        $created_at
 * @property Carbon        $updated_at
 * @property User|null     $reporter
 * @property User|null     $resolvedBy
 * @property Feedback|null $feedback
 */
class FeedbackReport extends AbstractModel
{
    protected $table = 'tfb_reports';

    public $timestamps = true;

    protected $fillable = [
        'feedback_id',
        'user_id',
        'reason',
        'resolved',
        'resolved_by_id',
    ];

    protected $casts = [
        'resolved'   => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Feedback relationship.
     */
    public function feedback()
    {
        return $this->belongsTo(Feedback::class, 'feedback_id');
    }

    /**
     * The user who submitted the report.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Resolved by relationship.
     */
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by_id');
    }
}
