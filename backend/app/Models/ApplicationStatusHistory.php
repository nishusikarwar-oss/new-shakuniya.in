<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'application_status_history';

    public $timestamps = false; // Using changed_at instead

    protected $fillable = [
        'application_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    /**
     * Get the application that this history belongs to
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'application_id', 'id');
    }

    /**
     * Get old status label
     */
    public function getOldStatusLabelAttribute(): string
    {
        return JobApplication::STATUSES[$this->old_status] ?? ucfirst($this->old_status);
    }

    /**
     * Get new status label
     */
    public function getNewStatusLabelAttribute(): string
    {
        return JobApplication::STATUSES[$this->new_status] ?? ucfirst($this->new_status);
    }

    /**
     * Get old status color
     */
    public function getOldStatusColorAttribute(): string
    {
        return JobApplication::STATUS_COLORS[$this->old_status] ?? 'gray';
    }

    /**
     * Get new status color
     */
    public function getNewStatusColorAttribute(): string
    {
        return JobApplication::STATUS_COLORS[$this->new_status] ?? 'gray';
    }

    /**
     * Get changed by name (if it's an email, mask it)
     */
    public function getChangedByNameAttribute(): string
    {
        if (empty($this->changed_by)) {
            return 'System';
        }

        // If it's an email, mask it
        if (filter_var($this->changed_by, FILTER_VALIDATE_EMAIL)) {
            $parts = explode('@', $this->changed_by);
            $name = $parts[0];
            $domain = $parts[1] ?? '';
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 2));
            return $maskedName . '@' . $domain;
        }

        return $this->changed_by;
    }

    /**
     * Get formatted changed at time
     */
    public function getFormattedChangedAtAttribute(): string
    {
        return $this->changed_at->format('M d, Y h:i A');
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->changed_at->diffForHumans();
    }

    /**
     * Scope a query to filter by application
     */
    public function scopeForApplication($query, $applicationId)
    {
        return $query->where('application_id', $applicationId);
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeChangedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('changed_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by new status
     */
    public function scopeWithNewStatus($query, $status)
    {
        return $query->where('new_status', $status);
    }

    /**
     * Scope a query to filter by changed by
     */
    public function scopeChangedBy($query, $changedBy)
    {
        return $query->where('changed_by', $changedBy);
    }

    /**
     * Scope a query to order by latest first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('changed_at', 'desc');
    }
}