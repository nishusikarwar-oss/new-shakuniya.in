<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $primaryKey = 'service_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'services';

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'short_description',
        'full_description',
        'icon_name',
        'icon_url',
        'gradient_from',
        'gradient_to',
        'featured_image',
        'cta_text',
        'cta_link',
        'display_order',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'company_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'gradient_from' => 'from-purple-500',
        'gradient_to' => 'to-cyan-500',
        'cta_text' => 'Learn More',
        'is_active' => true,
        'display_order' => 0
    ];

    protected $appends = [
        'gradient_classes',
        'icon_url_full',
        'featured_image_full'
    ];

    /**
     * Get the features for the service
     */
    public function features(): HasMany
    {
        return $this->hasMany(ServiceFeature::class, 'service_id', 'service_id')
                    ->where('is_active', true)
                    ->orderBy('display_order', 'asc');
    }

    /**
     * Get all features (including inactive) for admin
     */
    public function allFeatures(): HasMany
    {
        return $this->hasMany(ServiceFeature::class, 'service_id', 'service_id')
                    ->orderBy('display_order', 'asc');
    }

    /**
     * Get the company that owns the service
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Accessors
     */
    public function getIconUrlFullAttribute(): ?string
    {
        if (!$this->icon_url) {
            return null;
        }
        
        if (str_starts_with($this->icon_url, 'http')) {
            return $this->icon_url;
        }
        
        return asset('storage/' . $this->icon_url);
    }

    public function getFeaturedImageFullAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }
        
        if (str_starts_with($this->featured_image, 'http')) {
            return $this->featured_image;
        }
        
        return asset('storage/' . $this->featured_image);
    }

    public function getIconUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }

    public function getFeaturedImageAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }

    public function getGradientClassesAttribute(): string
    {
        return $this->gradient_from . ' ' . $this->gradient_to;
    }

    public function getGradientArrayAttribute(): array
    {
        return [
            'from' => $this->gradient_from,
            'to' => $this->gradient_to
        ];
    }

    /**
     * Generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = str()->slug($service->title);
            }
        });

        static::updating(function ($service) {
            if ($service->isDirty('title') && !$service->isDirty('slug')) {
                $service->slug = str()->slug($service->title);
            }
        });

        static::deleting(function ($service) {
            // Delete associated features
            $service->features()->delete();
        });
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')
                     ->orderBy('title', 'asc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('short_description', 'LIKE', "%{$search}%")
              ->orWhere('full_description', 'LIKE', "%{$search}%")
              ->orWhere('meta_keywords', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Helper Methods
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured === true;
    }

    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    public function toggleActive(): bool
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }

    public function toggleFeatured(): bool
    {
        $this->is_featured = !$this->is_featured;
        return $this->save();
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}