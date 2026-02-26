<?php
// app/Models/JobApplication.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use HasFactory;

    protected $table = 'job_applications';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'job_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'current_company',
        'current_position',
        'experience_years',
        'highest_qualification',
        'resume_path',
        'cover_letter',
        'portfolio_url',
        'linkedin_url',
        'github_url',
        'expected_salary',
        'notice_period',
        'willing_to_relocate',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
        'applied_at'
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'willing_to_relocate' => 'boolean',
        'applied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'pending'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_SHORTLISTED = 'shortlisted';
    const STATUS_INTERVIEWED = 'interviewed';
    const STATUS_OFFERED = 'offered';
    const STATUS_HIRED = 'hired';
    const STATUS_REJECTED = 'rejected';

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_REVIEWED => 'Reviewed',
        self::STATUS_SHORTLISTED => 'Shortlisted',
        self::STATUS_INTERVIEWED => 'Interviewed',
        self::STATUS_OFFERED => 'Offered',
        self::STATUS_HIRED => 'Hired',
        self::STATUS_REJECTED => 'Rejected'
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_REVIEWED => 'info',
        self::STATUS_SHORTLISTED => 'primary',
        self::STATUS_INTERVIEWED => 'purple',
        self::STATUS_OFFERED => 'success',
        self::STATUS_HIRED => 'success',
        self::STATUS_REJECTED => 'danger'
    ];

    /**
     * Get the job that this application belongs to
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_id', 'id');
    }

    /**
     * Get the status history for this application
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class, 'application_id', 'id')
                    ->orderBy('changed_at', 'desc');
    }

    /**
     * Get the latest status history
     */
    public function getLatestStatusHistoryAttribute()
    {
        return $this->statusHistory()->first();
    }

    /**
     * Get status change timeline
     */
    public function getStatusTimelineAttribute()
    {
        return $this->statusHistory->map(function($history) {
            return [
                'id' => $history->id,
                'old_status' => $history->old_status,
                'old_status_label' => $history->old_status_label,
                'old_status_color' => $history->old_status_color,
                'new_status' => $history->new_status,
                'new_status_label' => $history->new_status_label,
                'new_status_color' => $history->new_status_color,
                'notes' => $history->notes,
                'changed_by' => $history->changed_by_name,
                'changed_at' => $history->changed_at,
                'formatted_changed_at' => $history->formatted_changed_at,
                'time_ago' => $history->time_ago
            ];
        });
    }

    /**
     * Add status history
     */
    public function addStatusHistory($newStatus, $notes = null, $changedBy = null)
    {
        $oldStatus = $this->status;
        
        return $this->statusHistory()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'changed_by' => $changedBy ?? auth()->user()?->email ?? 'system',
            'changed_at' => now()
        ]);
    }

    /**
     * Update status with history tracking
     */
    public function updateStatusWithHistory($newStatus, $notes = null, $changedBy = null)
    {
        if ($this->status === $newStatus) {
            return false;
        }

        $history = $this->addStatusHistory($newStatus, $notes, $changedBy);
        
        $this->status = $newStatus;
        $this->save();
        
        return $history;
    }

    /**
     * Get candidate's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get resume URL
     */
    public function getResumeUrlAttribute(): ?string
    {
        if ($this->resume_path) {
            return asset('storage/' . $this->resume_path);
        }
        return null;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $color = self::STATUS_COLORS[$this->status] ?? 'secondary';
        $label = self::STATUSES[$this->status] ?? ucfirst($this->status);
        
        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

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
     * Get masked phone for privacy
     */
    public function getMaskedPhoneAttribute(): string
    {
        return substr($this->phone, 0, 4) . '****' . substr($this->phone, -2);
    }

    /**
     * Get application age in days
     */
    public function getAgeInDaysAttribute(): int
    {
        return $this->applied_at->diffInDays(now());
    }

    /**
     * Check if application is still pending
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if application is reviewed
     */
    public function getIsReviewedAttribute(): bool
    {
        return in_array($this->status, [
            self::STATUS_REVIEWED,
            self::STATUS_SHORTLISTED,
            self::STATUS_INTERVIEWED
        ]);
    }

    /**
     * Check if application is successful
     */
    public function getIsSuccessfulAttribute(): bool
    {
        return in_array($this->status, [
            self::STATUS_OFFERED,
            self::STATUS_HIRED
        ]);
    }

    /**
     * Check if application is rejected
     */
    public function getIsRejectedAttribute(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Scope a query to filter by status
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to get applications by job
     */
    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeAppliedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('applied_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to search applications
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('current_company', 'LIKE', "%{$search}%")
              ->orWhere('current_position', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by experience
     */
    public function scopeMinExperience($query, $years)
    {
        return $query->where('experience_years', '>=', $years);
    }

    /**
     * Scope a query to filter by relocation preference
     */
    public function scopeWillingToRelocate($query)
    {
        return $query->where('willing_to_relocate', true);
    }
}