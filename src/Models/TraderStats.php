<?php

namespace HuseyinFiliz\TraderFeedback\Models;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class TraderStats extends AbstractModel
{
    protected $table = 'tfb_stats';

    protected $fillable = [
        'user_id',
        'positive_count',
        'negative_count',
        'neutral_count',
        'score',
        'last_updated'
    ];

    protected $casts = [
        'positive_count' => 'integer',
        'negative_count' => 'integer',
        'neutral_count' => 'integer',
        'score' => 'float',
        'last_updated' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}