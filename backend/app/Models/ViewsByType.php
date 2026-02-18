<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewsByType extends Model
{
    protected $table = 'views_by_type';
    
    public $timestamps = false;
    
    protected $fillable = [
        'stat_date',
        'viewable_type',
        'total_views',
        'unique_views',
        'percentage_of_total'
    ];

    protected $casts = [
        'stat_date' => 'date',
        'total_views' => 'integer',
        'unique_views' => 'integer',
        'percentage_of_total' => 'float'
    ];

    /**
     * Get stats by type for a date range
     */
    public static function getByType($fromDate, $toDate = null)
    {
        $toDate = $toDate ?? now()->toDateString();
        
        return self::whereBetween('stat_date', [$fromDate, $toDate])
            ->orderBy('stat_date', 'desc')
            ->get()
            ->groupBy('viewable_type');
    }
}