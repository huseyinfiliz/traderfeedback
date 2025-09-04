<?php

namespace HuseyinFiliz\TraderFeedback\Models;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class FeedbackReport extends AbstractModel
{
    protected $table = 'trader_feedback_reports';

    protected $fillable = [
        'user_id',
        'feedback_id',
        'reason',
        'resolved',
        'resolved_by_id'
    ];

    protected $casts = [
        'resolved' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by_id');
    }
}