<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    /**
 * Get the features for the service
 */
public function features()
{
    return $this->hasMany(ServiceFeature::class, 'service_id', 'service_id')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all features (including inactive) for admin
 */
public function allFeatures()
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
     * Get the full icon URL
     */
    public function getIconUrlAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }

    /**
     * Get the full featured image URL
     */
    public function getFeaturedImageAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }

    /**
     * Get gradient classes as array
     */
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
    }
}
