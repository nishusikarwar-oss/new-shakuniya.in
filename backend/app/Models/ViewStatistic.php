<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewStatistic extends Model
{
    protected $table = 'view_statistics';
    
    public $timestamps = false;
    
    protected $fillable = [
        'stat_date',
        'total_views',
        'unique_views',
        'desktop_views',
        'mobile_views',
        'tablet_views',
        'guest_views',
        'logged_in_views',
        'average_duration',
        'percentage_change'
    ];

    protected $casts = [
        'stat_date' => 'date',
        'total_views' => 'integer',
        'unique_views' => 'integer',
        'desktop_views' => 'integer',
        'mobile_views' => 'integer',
        'tablet_views' => 'integer',
        'guest_views' => 'integer',
        'logged_in_views' => 'integer',
        'average_duration' => 'float',
        'percentage_change' => 'float'
    ];

    /**
     * Get today's statistics
     */
    public static function getTodayStats()
    {
        return self::firstOrCreate(
            ['stat_date' => now()->toDateString()],
            [
                'total_views' => 0,
                'unique_views' => 0,
                'desktop_views' => 0,
                'mobile_views' => 0,
                'tablet_views' => 0,
                'guest_views' => 0,
                'logged_in_views' => 0,
                'average_duration' => 0,
                'percentage_change' => 0
            ]
        );
    }

    /**
     * Calculate percentage change from previous day
     */
    public static function calculatePercentageChange($current, $previous)
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 2);
        }
        return $current > 0 ? 100 : 0;
    }

    /**
     * Get trend data with percentages
     */
    public static function getTrendData($days = 30)
    {
        $stats = self::where('stat_date', '>=', now()->subDays($days))
            ->orderBy('stat_date', 'desc')
            ->get();

        $trendData = [];
        foreach ($stats as $index => $stat) {
            $previousViews = $index < count($stats) - 1 ? $stats[$index + 1]->total_views : 0;
            $percentageChange = self::calculatePercentageChange($stat->total_views, $previousViews);
            
            $trendData[] = [
                'date' => $stat->stat_date->format('Y-m-d'),
                'views' => $stat->total_views,
                'unique_views' => $stat->unique_views,
                'percentage_change' => $percentageChange,
                'trend' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral'),
                'display' => $stat->total_views . ' ' . 
                    ($percentageChange > 0 ? '↑+' : ($percentageChange < 0 ? '↓' : '')) . 
                    abs($percentageChange) . '%'
            ];
        }

        return $trendData;
    }
}