<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailStatistic extends Model
{
    protected $table = 'email_statistics';
    
    public $timestamps = false;
    
    protected $fillable = [
        'stat_date',
        'total_sent',
        'total_delivered',
        'total_opened',
        'total_clicked',
        'unique_opens',
        'unique_clicks',
        'open_rate',
        'click_rate',
        'views_count',
        'percentage_change'
    ];

    protected $casts = [
        'stat_date' => 'date',
        'total_sent' => 'integer',
        'total_delivered' => 'integer',
        'total_opened' => 'integer',
        'total_clicked' => 'integer',
        'unique_opens' => 'integer',
        'unique_clicks' => 'integer',
        'open_rate' => 'float',
        'click_rate' => 'float',
        'views_count' => 'integer',
        'percentage_change' => 'float'
    ];

    public static function getTodayStats()
    {
        return self::firstOrCreate(
            ['stat_date' => now()->toDateString()],
            [
                'total_sent' => 0,
                'total_delivered' => 0,
                'total_opened' => 0,
                'total_clicked' => 0,
                'unique_opens' => 0,
                'unique_clicks' => 0,
                'views_count' => 0
            ]
        );
    }

    public static function calculatePercentageChange($current, $previous)
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 2);
        }
        return $current > 0 ? 100 : 0;
    }

    public static function getTrendData($days = 30)
    {
        $stats = self::where('stat_date', '>=', now()->subDays($days))
            ->orderBy('stat_date', 'desc')
            ->get();

        $trendData = [];
        foreach ($stats as $index => $stat) {
            $previousViews = $index < count($stats) - 1 ? $stats[$index + 1]->views_count : 0;
            $percentageChange = self::calculatePercentageChange($stat->views_count, $previousViews);
            
            $trendData[] = [
                'date' => $stat->stat_date->format('Y-m-d'),
                'views' => $stat->views_count,
                'sent' => $stat->total_sent,
                'opened' => $stat->total_opened,
                'unique_opens' => $stat->unique_opens,
                'percentage_change' => $percentageChange,
                'trend' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral'),
                'display' => $stat->views_count . ' ' . 
                    ($percentageChange > 0 ? '↑+' : ($percentageChange < 0 ? '↓' : '')) . 
                    abs($percentageChange) . '%'
            ];
        }

        return $trendData;
    }
}