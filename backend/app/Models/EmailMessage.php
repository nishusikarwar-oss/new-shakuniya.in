<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailMessage extends Model
{
    protected $table = 'email_messages';
    
    protected $fillable = [
        'subject',
        'message_content',
        'sender_email',
        'sender_name',
        'recipient_email',
        'recipient_name',
        'status',
        'opens_count',
        'unique_opens',
        'clicks_count',
        'views_count',
        'opened_at',
        'clicked_at',
        'sent_at',
        'delivered_at'
    ];

    protected $casts = [
        'opens_count' => 'integer',
        'unique_opens' => 'integer',
        'clicks_count' => 'integer',
        'views_count' => 'integer',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function opens(): HasMany
    {
        return $this->hasMany(EmailOpen::class, 'email_message_id');
    }

    public function trackOpen(array $data = []): ?EmailOpen
    {
        try {
            $ip = $data['ip'] ?? request()->ip();
            
            $isUnique = !$this->opens()->where('ip_address', $ip)->exists();
            
            $open = $this->opens()->create([
                'opened_at' => now(),
                'ip_address' => $ip,
                'user_agent' => $data['user_agent'] ?? request()->userAgent(),
                'device_type' => $data['device_type'] ?? $this->detectDeviceType(),
                'location' => $data['location'] ?? null,
                'is_unique' => $isUnique
            ]);

            $this->increment('opens_count');
            $this->increment('views_count');
            
            if ($isUnique) {
                $this->increment('unique_opens');
            }
            
            $this->opened_at = now();
            $this->save();

            return $open;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function detectDeviceType(): string
    {
        $userAgent = request()->userAgent() ?? '';
        
        if (preg_match('/(mobile|iphone|ipod|android)/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/(tablet|ipad)/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}