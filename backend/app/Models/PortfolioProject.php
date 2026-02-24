<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioProject extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'portfolio_projects';

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'description',
        'category',
        'client_name',
        'completion_date',
        'project_url',
        'featured_image',
        'technologies',
        'is_featured',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'company_id' => 'integer',
        'completion_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
        'display_order' => 0,
        'company_id' => 1
    ];

    /**
     * Get the company that owns the project
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Get the full featured image URL
     */
    public function getFeaturedImageAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value ?? asset('images/default-project.jpg');
    }

    /**
     * Get technologies as array
     */
    public function getTechnologiesArrayAttribute(): array
    {
        if (empty($this->technologies)) {
            return [];
        }

        // If stored as JSON
        if ($this->technologies[0] === '[' || $this->technologies[0] === '{') {
            return json_decode($this->technologies, true) ?? [];
        }

        // If stored as comma-separated string
        return array_map('trim', explode(',', $this->technologies));
    }

    /**
     * Set technologies from array
     */
    public function setTechnologiesFromArray(array $technologies): void
    {
        $this->technologies = json_encode($technologies);
    }

    /**
     * Get formatted completion date
     */
    public function getFormattedCompletionDateAttribute(): ?string
    {
        return $this->completion_date?->format('F Y');
    }

    /**
     * Get project year
     */
    public function getYearAttribute(): ?int
    {
        return $this->completion_date?->year;
    }

    /**
     * Get project excerpt
     */
    public function getExcerptAttribute($length = 150): string
    {
        return strlen($this->description) > $length 
            ? substr($this->description, 0, $length) . '...' 
            : $this->description;
    }

    /**
     * Generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = str()->slug($project->title);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && !$project->isDirty('slug')) {
                $project->slug = str()->slug($project->title);
            }
        });
    }

    /**
     * Scope a query to only active projects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only featured projects
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Scope a query to filter by category
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by year
     */
    public function scopeFromYear($query, $year)
    {
        return $query->whereYear('completion_date', $year);
    }

    /**
     * Scope a query to search projects
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('client_name', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%");
        });
    }
}