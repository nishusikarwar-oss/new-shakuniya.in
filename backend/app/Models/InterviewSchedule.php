<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewSchedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'interview_schedules';

    protected $fillable = [
        'application_id',
        'interview_date',
        'interview_time',
        'interview_type',
        'interview_link',
        'interview_location',
        'interviewer_name',
        'interviewer_email',
        'meeting_platform',
        'meeting_id',
        'meeting_password',
        'additional_details',
        'status',
        'feedback',
        'rating'
    ];

    protected $casts = [
        'interview_date' => 'date',
        'interview_time' => 'datetime:H:i',
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'scheduled',
        'interview_type' => 'online',
        'meeting_platform' => 'google-meet'
    ];

    const INTERVIEW_TYPES = [
        'online' => 'Online',
        'in-person' => 'In-Person',
        'phone' => 'Phone'
    ];

    const MEETING_PLATFORMS = [
        'google-meet' => 'Google Meet',
        'zoom' => 'Zoom',
        'teams' => 'Microsoft Teams',
        'other' => 'Other'
    ];

    const STATUSES = [
        'scheduled' => 'Scheduled',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'rescheduled' => 'Rescheduled'
    ];

    const STATUS_COLORS = [
        'scheduled' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger',
        'rescheduled' => 'warning'
    ];

    /**
     * Get the application that this interview belongs to
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'application_id', 'id');
    }

    /**
     * Get formatted interview datetime
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->interview_date->format('M d, Y') . ' at ' . $this->interview_time->format('h:i A');
    }

    /**
     * Get interview date with day name
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->interview_date->format('l, M d, Y');
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->interview_time->format('h:i A');
    }

    /**
     * Get interview type label
     */
    public function getInterviewTypeLabelAttribute(): string
    {
        return self::INTERVIEW_TYPES[$this->interview_type] ?? ucfirst($this->interview_type);
    }

    /**
     * Get meeting platform label
     */
    public function getMeetingPlatformLabelAttribute(): string
    {
        return self::MEETING_PLATFORMS[$this->meeting_platform] ?? ucfirst($this->meeting_platform);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $color = $this->status_color;
        $label = $this->status_label;
        
        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    /**
     * Get interview link or location
     */
    public function getJoinLinkAttribute(): ?string
    {
        if ($this->interview_type === 'online' && $this->interview_link) {
            return $this->interview_link;
        }
        
        if ($this->interview_type === 'in-person' && $this->interview_location) {
            return $this->interview_location;
        }
        
        return null;
    }

    /**
     * Get meeting details array
     */
    public function getMeetingDetailsAttribute(): array
    {
        if ($this->interview_type !== 'online') {
            return [];
        }

        return [
            'platform' => $this->meeting_platform_label,
            'link' => $this->interview_link,
            'meeting_id' => $this->meeting_id,
            'password' => $this->meeting_password
        ];
    }

    /**
     * Check if interview is upcoming
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->status === 'scheduled' && 
               ($this->interview_date > now() || 
                ($this->interview_date == now() && $this->interview_time > now()));
    }

    /**
     * Check if interview is today
     */
    public function getIsTodayAttribute(): bool
    {
        return $this->interview_date->isToday();
    }

    /**
     * Check if interview is tomorrow
     */
    public function getIsTomorrowAttribute(): bool
    {
        return $this->interview_date->isTomorrow();
    }

    /**
     * Get countdown days
     */
    public function getDaysUntilAttribute(): ?int
    {
        if ($this->status !== 'scheduled') {
            return null;
        }
        
        return now()->diffInDays($this->interview_date, false);
    }

    /**
     * Scope a query to only upcoming interviews
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where(function($q) {
                $q->whereDate('interview_date', '>', now())
                  ->orWhere(function($q2) {
                      $q2->whereDate('interview_date', now())
                         ->whereTime('interview_time', '>', now());
                  });
            })
            ->orderBy('interview_date')
            ->orderBy('interview_time');
    }

    /**
     * Scope a query to only past interviews
     */
    public function scopePast($query)
    {
        return $query->where('status', 'completed')
            ->orWhere(function($q) {
                $q->where('status', 'scheduled')
                  ->where(function($q2) {
                      $q2->whereDate('interview_date', '<', now())
                        ->orWhere(function($q3) {
                            $q3->whereDate('interview_date', now())
                               ->whereTime('interview_time', '<=', now());
                        });
                  });
            })
            ->orderBy('interview_date', 'desc')
            ->orderBy('interview_time', 'desc');
    }

    /**
     * Scope a query to filter by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('interview_date', $date);
    }

    /**
     * Scope a query to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('interview_type', $type);
    }

    /**
     * Scope a query to filter by interviewer
     */
    public function scopeWithInterviewer($query, $interviewer)
    {
        return $query->where('interviewer_name', 'LIKE', "%{$interviewer}%")
            ->orWhere('interviewer_email', 'LIKE', "%{$interviewer}%");
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('interview_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to order by upcoming
     */
    public function scopeOrderByUpcoming($query)
    {
        return $query->orderBy('interview_date')->orderBy('interview_time');
    }
}