<?php

namespace HuseyinFiliz\TraderFeedback\Models;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class FeedbackReport extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'tfb_reports';

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'feedback_id',
        'reason',
        'resolved',
        'resolved_by_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'resolved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who reported.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reported feedback.
     */
    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    /**
     * Get the user who resolved the report.
     */
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by_id');
    }
}