<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB; // <-- ADD THIS LINE

class EmailOpen extends Model
{
    protected $table = 'email_opens';
    
    public $timestamps = false;
    
    protected $fillable = [
        'email_message_id',
        'opened_at',
        'ip_address',
        'user_agent',
        'device_type',
        'location',
        'is_unique'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'is_unique' => 'boolean'
    ];

    public function emailMessage(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class);
    }

    public function scopeUnique($query)
    {
        return $query->where('is_unique', true);
    }

    public function scopeByDevice($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public static function getDeviceBreakdown($days = 30)
    {
        return self::select('device_type', DB::raw('count(*) as total'))
            ->where('opened_at', '>=', now()->subDays($days))
            ->groupBy('device_type')
            ->get();
    }
}