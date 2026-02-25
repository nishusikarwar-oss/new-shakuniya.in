<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'locations';

    public $timestamps = false; // Only created_at exists, no updated_at

    protected $fillable = [
        'city',
        'state',
        'country',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'country' => 'India'
    ];

    /**
     * Get the jobs for this location
     */
    public function jobs()
    {
        return $this->hasMany(JobOpening::class, 'location_id', 'id');
    }

    /**
     * Get active jobs count
     */
    public function getActiveJobsCountAttribute(): int
    {
        return $this->jobs()->where('is_active', true)->count();
    }

    /**
     * Get total jobs count
     */
    public function getTotalJobsCountAttribute(): int
    {
        return $this->jobs()->count();
    }

    /**
     * Get full location name (City, State)
     */
    public function getFullNameAttribute(): string
    {
        if ($this->state) {
            return $this->city . ', ' . $this->state;
        }
        return $this->city;
    }

    /**
     * Get full location with country
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->city;
        
        if ($this->state) {
            $address .= ', ' . $this->state;
        }
        
        if ($this->country) {
            $address .= ', ' . $this->country;
        }
        
        return $address;
    }

    /**
     * Scope a query to only active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by country
     */
    public function scopeInCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope a query to filter by state
     */
    public function scopeInState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope a query to search locations
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('city', 'LIKE', "%{$search}%")
              ->orWhere('state', 'LIKE', "%{$search}%")
              ->orWhere('country', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get locations grouped by country
     */
    public static function getGroupedByCountry()
    {
        $locations = self::active()
            ->orderBy('country')
            ->orderBy('state')
            ->orderBy('city')
            ->get();

        $grouped = [];
        
        foreach ($locations as $location) {
            $country = $location->country ?? 'Other';
            
            if (!isset($grouped[$country])) {
                $grouped[$country] = [
                    'name' => $country,
                    'states' => []
                ];
            }
            
            $state = $location->state ?? 'Other';
            
            if (!isset($grouped[$country]['states'][$state])) {
                $grouped[$country]['states'][$state] = [
                    'name' => $state,
                    'cities' => []
                ];
            }
            
            $grouped[$country]['states'][$state]['cities'][] = [
                'id' => $location->id,
                'name' => $location->city,
                'full_name' => $location->full_name
            ];
        }

        // Convert to indexed array
        foreach ($grouped as &$country) {
            $country['states'] = array_values($country['states']);
        }
        
        return array_values($grouped);
    }
}