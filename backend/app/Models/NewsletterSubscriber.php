<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $primaryKey = 'subscriber_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'newsletter_subscribers';

    public $timestamps = false; // Using subscribed_at and unsubscribed_at instead

    protected $fillable = [
        'email',
        'name',
        'is_active',
        'subscribed_at',
        'unsubscribed_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true
    ];

    /**
     * Get masked email for privacy
     */
    public function getMaskedEmailAttribute(): string
    {
        $parts = explode('@', $this->email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 2));
        
        return $maskedName . '@' . $domain;
    }

    /**
     * Get subscription duration in days
     */
    public function getSubscriptionDurationAttribute(): int
    {
        if (!$this->subscribed_at) {
            return 0;
        }

        $endDate = $this->unsubscribed_at ?? now();
        return $this->subscribed_at->diffInDays($endDate);
    }

    /**
     * Check if subscriber is active
     */
    public function getIsActiveSubscriberAttribute(): bool
    {
        return $this->is_active && !$this->unsubscribed_at;
    }

    /**
     * Scope a query to only active subscribers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('unsubscribed_at');
    }

    /**
     * Scope a query to only inactive subscribers
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false)->orWhereNotNull('unsubscribed_at');
    }

    /**
     * Scope a query to search by email or name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('email', 'LIKE', "%{$search}%")
              ->orWhere('name', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeSubscribedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('subscribed_at', [$startDate, $endDate]);
    }

    /**
     * Unsubscribe a subscriber
     */
    public function unsubscribe(): bool
    {
        $this->is_active = false;
        $this->unsubscribed_at = now();
        return $this->save();
    }

    /**
     * Resubscribe a subscriber
     */
    public function resubscribe(): bool
    {
        $this->is_active = true;
        $this->unsubscribed_at = null;
        return $this->save();
    }
}