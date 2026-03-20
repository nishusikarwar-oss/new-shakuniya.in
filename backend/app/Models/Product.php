<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'products';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'slug',
        'title',
        'short_description',
        'full_description',
        'price_usd',
        'price_inr',
        'image',
        'video_url',
        'video_text',
        'is_active',
        'display_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        // 'tags',
        
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'price_usd' => 'decimal:2',
        'price_inr' => 'decimal:2',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'display_order' => 0,
        'price_usd' => 0,
        'price_inr' => 0
    ];

    protected $appends = [
        'primary_image_url',
    ];

    // ============ RELATIONSHIPS ============

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(ProductFeature::class, 'product_id', 'id')
                    ->orderBy('display_order', 'asc');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id')
                    ->orderBy('display_order', 'asc');
    }

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(ProductPricingTier::class, 'product_id', 'id')
                    ->orderBy('display_order', 'asc');
    }

    // ============ ACCESSORS ============

    public function getPrimaryImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http')) return $this->image;
        return asset('storage/' . $this->image);
    }

    // public function getOgImageUrlAttribute(): ?string
    // {
    //     if (!$this->og_image) return null;
    //     if (str_starts_with($this->og_image, 'http')) return $this->og_image;
    //     return asset('storage/' . $this->og_image);
    // }

    // public function getTwitterImageUrlAttribute(): ?string
    // {
    //     if (!$this->twitter_image) return null;
    //     if (str_starts_with($this->twitter_image, 'http')) return $this->twitter_image;
    //     return asset('storage/' . $this->twitter_image);
    // }

    // ============ SCOPES ============

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('short_description', 'like', "%{$term}%");
            //   ->orWhere('tags', 'like', "%{$term}%");
        });
    }
}
