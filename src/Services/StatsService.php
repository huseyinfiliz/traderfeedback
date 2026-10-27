<?php

namespace HuseyinFiliz\TraderFeedback\Services;

use Carbon\Carbon;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\ConnectionInterface;

class StatsService
{
    public function __construct(protected ConnectionInterface $db, protected Repository $cache)
    {
    }

    public function forUser(int $userId): TraderStats
    {
        User::findOrFail($userId);

        return TraderStats::where('user_id', $userId)->first() ?? $this->calculate($userId);
    }

    public function updateUserStats(int $userId): TraderStats
    {
        $stats = $this->db->transaction(function () use ($userId) {
            // Lock the parent row so first-time upserts and concurrent recalculations cannot race.
            User::whereKey($userId)->select('id')->lockForUpdate()->firstOrFail();
            $calculated = $this->calculate($userId);
            $stats = TraderStats::firstOrNew(['user_id' => $userId]);
            $stats->positive_count = $calculated->positive_count;
            $stats->neutral_count = $calculated->neutral_count;
            $stats->negative_count = $calculated->negative_count;
            $stats->score = $calculated->score;
            $stats->last_updated = Carbon::now();
            $stats->save();

            return $stats;
        }, 3);

        $this->clearCache($userId);

        return $stats;
    }

    public function clearCache(int $userId): void
    {
        // Invalidate entries written by earlier versions during rolling upgrades.
        $this->cache->forget("trader_stats_{$userId}");
    }

    private function calculate(int $userId): TraderStats
    {
        $counts = Feedback::where('to_user_id', $userId)->where('is_approved', true)
            ->selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type');

        $stats = new TraderStats();
        $stats->user_id = $userId;
        $stats->positive_count = (int) ($counts[Feedback::TYPE_POSITIVE] ?? 0);
        $stats->neutral_count = (int) ($counts[Feedback::TYPE_NEUTRAL] ?? 0);
        $stats->negative_count = (int) ($counts[Feedback::TYPE_NEGATIVE] ?? 0);
        $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
        $stats->score = $total ? 100 * $stats->positive_count / $total : 0;

        return $stats;
    }
}
