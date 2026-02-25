<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAlert extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'job_alerts';

    protected $fillable = [
        'email',
        'name',
        'preferred_location',
        'preferred_category',
        'experience_years',
        'frequency',
        'is_active'
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'frequency' => 'weekly'
    ];

    const FREQUENCIES = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'instant' => 'Instant'
    ];

    const FREQUENCY_COLORS = [
        'daily' => 'blue',
        'weekly' => 'green',
        'instant' => 'purple'
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
     * Get frequency label
     */
    public function getFrequencyLabelAttribute(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? ucfirst($this->frequency);
    }

    /**
     * Get frequency color
     */
    public function getFrequencyColorAttribute(): string
    {
        return self::FREQUENCY_COLORS[$this->frequency] ?? 'gray';
    }

    /**
     * Get preferred location array
     */
    public function getPreferredLocationArrayAttribute(): array
    {
        if (empty($this->preferred_location)) {
            return [];
        }
        
        return array_map('trim', explode(',', $this->preferred_location));
    }

    /**
     * Get preferred category array
     */
    public function getPreferredCategoryArrayAttribute(): array
    {
        if (empty($this->preferred_category)) {
            return [];
        }
        
        return array_map('trim', explode(',', $this->preferred_category));
    }

    /**
     * Check if alert is for instant notifications
     */
    public function getIsInstantAttribute(): bool
    {
        return $this->frequency === 'instant';
    }

    /**
     * Check if alert is for daily notifications
     */
    public function getIsDailyAttribute(): bool
    {
        return $this->frequency === 'daily';
    }

    /**
     * Check if alert is for weekly notifications
     */
    public function getIsWeeklyAttribute(): bool
    {
        return $this->frequency === 'weekly';
    }

    /**
     * Get alert age in days
     */
    public function getAgeInDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Scope a query to only active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by frequency
     */
    public function scopeOfFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    /**
     * Scope a query to filter by location
     */
    public function scopeWithLocation($query, $location)
    {
        return $query->where('preferred_location', 'LIKE', "%{$location}%");
    }

    /**
     * Scope a query to filter by category
     */
    public function scopeWithCategory($query, $category)
    {
        return $query->where('preferred_category', 'LIKE', "%{$category}%");
    }

    /**
     * Scope a query to filter by experience
     */
    public function scopeMinExperience($query, $years)
    {
        return $query->where('experience_years', '>=', $years);
    }

    /**
     * Scope a query to search alerts
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('email', 'LIKE', "%{$search}%")
              ->orWhere('name', 'LIKE', "%{$search}%")
              ->orWhere('preferred_location', 'LIKE', "%{$search}%")
              ->orWhere('preferred_category', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get alerts by frequency for sending notifications
     */
    public static function getAlertsForFrequency(string $frequency)
    {
        return self::active()
            ->ofFrequency($frequency)
            ->get();
    }

    /**
     * Get matching jobs for this alert
     */
    public function getMatchingJobs()
    {
        $query = JobOpening::where('is_active', true)
            ->where('application_deadline', '>=', now());

        // Filter by location if specified
        if ($this->preferred_location) {
            $locations = $this->preferred_location_array;
            $query->where(function($q) use ($locations) {
                foreach ($locations as $location) {
                    $q->orWhere('location', 'LIKE', "%{$location}%");
                }
            });
        }

        // Filter by category if specified
        if ($this->preferred_category) {
            $categories = $this->preferred_category_array;
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('job_categories.name', $categories);
            });
        }

        // Filter by experience if specified
        if ($this->experience_years) {
            $query->where('experience_min', '<=', $this->experience_years)
                  ->where('experience_max', '>=', $this->experience_years);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}