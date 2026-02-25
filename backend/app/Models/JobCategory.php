<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'job_categories';

    public $timestamps = false; // Only created_at exists, no updated_at

    protected $fillable = [
        'name',
        'slug',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true
    ];

    /**
     * The jobs that belong to the category (Many-to-Many relationship)
     */
    public function jobs()
    {
        return $this->belongsToMany(JobOpening::class, 'job_category_mapping', 'category_id', 'job_id');
    }

    /**
     * Get active jobs in this category
     */
    public function getActiveJobsAttribute()
    {
        return $this->jobs()->where('is_active', true)->get();
    }

    /**
     * Get jobs count for this category
     */
    public function getJobsCountAttribute(): int
    {
        return $this->jobs()->count();
    }

    /**
     * Get active jobs count for this category
     */
    public function getActiveJobsCountAttribute(): int
    {
        return $this->jobs()->where('is_active', true)->count();
    }

    /**
     * Generate slug from name
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = str()->slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = str()->slug($category->name);
            }
        });
    }

    /**
     * Scope a query to only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search categories
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    /**
     * Scope a query to get categories with job counts
     */
    public function scopeWithJobCounts($query)
    {
        return $query->withCount(['jobs' => function($q) {
            $q->where('is_active', true);
        }]);
    }
}