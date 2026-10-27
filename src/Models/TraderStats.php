<?php

namespace HuseyinFiliz\TraderFeedback\Models;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;

/**
 * @property int         $user_id
 * @property int         $positive_count
 * @property int         $negative_count
 * @property int         $neutral_count
 * @property float       $score
 * @property Carbon|null $last_updated
 * @property User|null   $user
 */
class TraderStats extends AbstractModel
{
    protected $table = 'tfb_stats';

    protected $fillable = [
        'user_id',
        'positive_count',
        'negative_count',
        'neutral_count',
        'score',
        'last_updated',
    ];

    protected $casts = [
        'positive_count' => 'integer',
        'negative_count' => 'integer',
        'neutral_count'  => 'integer',
        'score'          => 'float',
        'last_updated'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
