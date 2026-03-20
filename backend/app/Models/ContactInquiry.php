<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInquiry extends Model
{
    use HasFactory;

    protected $primaryKey = 'inquiry_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'contact_inquiries';

    protected $fillable = [
        'company_name',
        'name',
        'email',
        'phone',
        'service_interest',
        'message',
        'status',
        'ip_address',
        'user_agent'
    ];


    protected $casts = [
        
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'company_name' => 1,
        'status' => 'pending'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_RESOLVED = 'resolved';

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONTACTED => 'Contacted',
        self::STATUS_RESOLVED => 'Resolved'
    ];

    /**
     * Get the company that owns the inquiry
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_name', 'company_name');
    }

    /**
     * Get formatted status with badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        $classes = [
            self::STATUS_PENDING => 'badge bg-warning',
            self::STATUS_CONTACTED => 'badge bg-info',
            self::STATUS_RESOLVED => 'badge bg-success'
        ];

        return '<span class="' . ($classes[$this->status] ?? 'badge bg-secondary') . '">' 
                . self::STATUSES[$this->status] . '</span>';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONTACTED => 'blue',
            self::STATUS_RESOLVED => 'green'
        ][$this->status] ?? 'gray';
    }

    /**
     * Get masked email (for privacy)
     */
    public function getMaskedEmailAttribute(): string
    {
        $parts = explode('@', $this->email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $maskedName = substr($name, 0, 3) . str_repeat('*', max(0, strlen($name) - 3));
        
        return $maskedName . '@' . $domain;
    }

    /**
     * Get masked phone (for privacy)
     */
    public function getMaskedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        return substr($this->phone, 0, 4) . '****' . substr($this->phone, -2);
    }

    /**
     * Get truncated message
     */
    public function getExcerptAttribute($length = 100): string
    {
        return strlen($this->message) > $length 
            ? substr($this->message, 0, $length) . '...' 
            : $this->message;
    }

    /**
     * Scope a query to filter by status
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get pending inquiries
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to get contacted inquiries
     */
    public function scopeContacted($query)
    {
        return $query->where('status', self::STATUS_CONTACTED);
    }

    /**
     * Scope a query to get resolved inquiries
     */
    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to search by name, email, phone or message
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('message', 'LIKE', "%{$search}%")
              ->orWhere('service_interest', 'LIKE', "%{$search}%");
        });
    }
}