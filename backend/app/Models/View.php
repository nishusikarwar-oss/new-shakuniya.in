<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $table = 'views';
    
    public $timestamps = false;
    
    protected $fillable = [
        'viewable_type',
        'viewable_id',
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'location',
        'country',
        'city',
        'is_unique',
        'session_id',
        'referer_url',
        'view_duration',
        'metadata',
        'created_at'
    ];

    protected $casts = [
        'is_unique' => 'boolean',
        'view_duration' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime'
    ];

    /**
     * Get the parent viewable model
     */
    public function viewable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who viewed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unique views
     */
    public function scopeUnique($query)
    {
        return $query->where('is_unique', true);
    }

    /**
     * Scope by device type
     */
    public function scopeByDevice($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }
}