<?php

namespace HuseyinFiliz\TraderFeedback\Services;

use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use Carbon\Carbon;

/**
 * Service for managing trader statistics
 */
class StatsService
{
    /**
     * Update user statistics based on their received feedbacks
     * Also clears the cache for this user
     *
     * @param int $userId
     * @return TraderStats
     */
    public static function updateUserStats(int $userId): TraderStats
    {
        // Clear cache for this user
        self::clearCache($userId);
        
        $stats = TraderStats::firstOrNew(['user_id' => $userId]);
        
        $stats->positive_count = Feedback::where('to_user_id', $userId)
            ->where('type', Feedback::TYPE_POSITIVE)
            ->where('is_approved', true)
            ->count();
            
        $stats->neutral_count = Feedback::where('to_user_id', $userId)
            ->where('type', Feedback::TYPE_NEUTRAL)
            ->where('is_approved', true)
            ->count();
            
        $stats->negative_count = Feedback::where('to_user_id', $userId)
            ->where('type', Feedback::TYPE_NEGATIVE)
            ->where('is_approved', true)
            ->count();
        
        $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
        $stats->score = $total > 0 ? ($stats->positive_count / $total) * 100 : 0;
        $stats->last_updated = Carbon::now();
        $stats->save();
        
        return $stats;
    }
    
    /**
     * Recalculate statistics for a user
     * Alias for updateUserStats for clarity
     *
     * @param int $userId
     * @return TraderStats
     */
    public static function recalculate(int $userId): TraderStats
    {
        return self::updateUserStats($userId);
    }
    
    /**
     * Clear cache for a specific user
     *
     * @param int $userId
     * @return void
     */
    public static function clearCache(int $userId): void
    {
        $cacheKey = "trader_stats_{$userId}";
        $cache = app('cache.store');
        
        if ($cache->has($cacheKey)) {
            $cache->forget($cacheKey);
        }
    }
    
    /**
     * Clear all trader stats cache
     * Note: This is a simple implementation that clears known keys
     * For production, consider using cache tags if available
     *
     * @return void
     */
    public static function clearAllCache(): void
    {
        // Get all user IDs that have stats
        $userIds = TraderStats::pluck('user_id')->toArray();
        
        foreach ($userIds as $userId) {
            self::clearCache($userId);
        }
    }
}