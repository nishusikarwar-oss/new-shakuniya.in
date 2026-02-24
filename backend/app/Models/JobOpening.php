<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOpening extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'job_openings';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'experience_required',
        'experience_min',
        'experience_max',
        'positions_available',
        'qualification',
        'location',
        'department_id',
        'employment_type',
        'work_type',
        'salary_range',
        'responsibilities',
        'requirements',
        'benefits',
        'application_deadline',
        'priority',
        'is_featured',
        'is_active',
        'view_count',
        'application_count',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'responsibilities' => 'array',
        'requirements' => 'array',
        'benefits' => 'array',
        'application_deadline' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'application_count' => 'integer',
        'priority' => 'integer',
        'experience_min' => 'integer',
        'experience_max' => 'integer',
        'positions_available' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
        'priority' => 0,
        'view_count' => 0,
        'application_count' => 0,
        'positions_available' => 1,
        'employment_type' => 'full-time',
        'work_type' => 'onsite'
    ];

    const EMPLOYMENT_TYPES = [
        'full-time' => 'Full Time',
        'part-time' => 'Part Time',
        'contract' => 'Contract',
        'internship' => 'Internship'
    ];

    const WORK_TYPES = [
        'onsite' => 'On Site',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid'
    ];

    /**
     * Get the department that owns the job opening
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * Get formatted experience range
     */
    public function getExperienceRangeAttribute(): string
    {
        if ($this->experience_min > 0 && $this->experience_max > 0) {
            return $this->experience_min . ' - ' . $this->experience_max . ' years';
        }
        return $this->experience_required;
    }

    /**
     * Check if job is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->application_deadline && $this->application_deadline->isPast();
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_active) {
            return '<span class="badge bg-secondary">Inactive</span>';
        }
        
        if ($this->is_expired) {
            return '<span class="badge bg-warning">Expired</span>';
        }
        
        return '<span class="badge bg-success">Active</span>';
    }

    /**
     * Get days remaining until deadline
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->application_deadline) {
            return null;
        }
        
        return now()->diffInDays($this->application_deadline, false);
    }

    /**
     * Get formatted salary range
     */
    public function getFormattedSalaryAttribute(): string
    {
        return $this->salary_range ?? 'Not Disclosed';
    }

    /**
     * Get application URL
     */
    public function getApplicationUrlAttribute(): string
    {
        return route('jobs.apply', $this->slug);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment application count
     */
    public function incrementApplicationCount(): void
    {
        $this->increment('application_count');
    }

    /**
     * Generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = str()->slug($job->title);
            }
        });

        static::updating(function ($job) {
            if ($job->isDirty('title') && !$job->isDirty('slug')) {
                $job->slug = str()->slug($job->title);
            }
        });
    }

    /**
     * Scope a query to only active jobs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('application_deadline')
                  ->orWhere('application_deadline', '>=', now());
            });
    }

    /**
     * Scope a query to only featured jobs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by employment type
     */
    public function scopeOfEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Scope a query to filter by work type
     */
    public function scopeOfWorkType($query, $type)
    {
        return $query->where('work_type', $type);
    }

    /**
     * Scope a query to filter by location
     */
    public function scopeInLocation($query, $location)
    {
        return $query->where('location', 'LIKE', "%{$location}%");
    }

    /**
     * Scope a query to filter by department
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by experience
     */
    public function scopeExperienceBetween($query, $min, $max)
    {
        return $query->where('experience_min', '>=', $min)
                     ->where('experience_max', '<=', $max);
    }

    /**
     * Scope a query to filter by min experience
     */
    public function scopeMinExperience($query, $years)
    {
        return $query->where('experience_min', '>=', $years);
    }

    /**
     * Scope a query to search jobs
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('short_description', 'LIKE', "%{$search}%")
              ->orWhere('location', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to order by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc')
                     ->orderBy('created_at', 'desc');
    }
}