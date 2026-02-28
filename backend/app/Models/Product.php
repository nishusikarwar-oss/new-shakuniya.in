<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // 👈 YEH IMPORT ADD KAREN

class Product extends Model
{
    use HasFactory, HasUuids;

    /**
     * Table configuration
     */
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Attributes that are mass assignable
     */
    protected $fillable = [
        'slug',
        'title',
        'short_description',
        'full_description',
        'price_usd',
        'price_inr',
        'image_url',
        'video_url',
        'is_active',
        'display_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_by',
        'updated_by'
    ];

    /**
     * Attributes that should be cast
     */
    protected $casts = [
        'price_usd' => 'decimal:2',
        'price_inr' => 'decimal:2',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Default attribute values
     */
    protected $attributes = [
        'is_active' => true,
        'display_order' => 0
    ];

    /**
     * Attributes to append to JSON
     */
    protected $appends = [
        'prices',
        'excerpt',
        'primary_image_url'
    ];

    /**
     * The relationships that should be eager loaded by default
     */
    protected $with = [
        'creator',
        'updater'
    ];

    // ============ RELATIONSHIPS ============

    /**
     * Get the user who created this product
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user who last updated this product
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Get the images for this product
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id')
                    ->orderBy('display_order', 'asc');
    }

    /**
     * Get the primary image for this product
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryImage(): HasOne // 👈 FIXED: HasMany → HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id')
                    ->where('is_primary', true);
    }

    /**
     * Get the features for this product
     */
    public function features(): HasMany
    {
        return $this->hasMany(ProductFeature::class, 'product_id', 'id')
                    ->where('is_active', true)
                    ->orderBy('display_order', 'asc');
    }

    /**
     * Get all features (including inactive)
     */
    public function allFeatures(): HasMany
    {
        return $this->hasMany(ProductFeature::class, 'product_id', 'id')
                    ->orderBy('display_order', 'asc');
    }

    /**
     * Get the pricing tiers for this product
     */
    public function pricingTiers(): HasMany
    {
        return $this->hasMany(ProductPricingTier::class, 'product_id', 'id')
                    ->where('is_active', true)
                    ->orderBy('display_order', 'asc');
    }

    /**
     * Get all pricing tiers (including inactive)
     */
    public function allPricingTiers(): HasMany
    {
        return $this->hasMany(ProductPricingTier::class, 'product_id', 'id')
                    ->orderBy('display_order', 'asc');
    }

    /**
     * Get popular pricing tier
     */
    public function popularTier(): HasOne // 👈 FIXED: HasMany → HasOne
    {
        return $this->hasOne(ProductPricingTier::class, 'product_id', 'id')
                    ->where('is_popular', true)
                    ->where('is_active', true);
    }

    /**
     * Get related products
     */
    public function relatedProducts(): HasMany
    {
        return $this->hasMany(RelatedProduct::class, 'product_id', 'id')
                    ->with('relatedProduct')
                    ->ordered();
    }

    /**
     * Get upsell products
     */
    public function upsells(): HasMany
    {
        return $this->relatedProducts()->where('relationship_type', 'upsell');
    }

    /**
     * Get cross-sell products
     */
    public function crossSells(): HasMany
    {
        return $this->relatedProducts()->where('relationship_type', 'cross-sell');
    }

    /**
     * Get alternative products
     */
    public function alternatives(): HasMany
    {
        return $this->relatedProducts()->where('relationship_type', 'alternative');
    }

    // ============ ACCESSORS ============

    /**
     * Get formatted price in USD
     */
    public function getFormattedPriceUsdAttribute(): ?string
    {
        return $this->price_usd ? '$' . number_format($this->price_usd, 2) : null;
    }

    /**
     * Get formatted price in INR
     */
    public function getFormattedPriceInrAttribute(): ?string
    {
        return $this->price_inr ? '₹' . number_format($this->price_inr, 2) : null;
    }

    /**
     * Get both prices
     */
    public function getPricesAttribute(): array
    {
        return [
            'usd' => [
                'raw' => $this->price_usd,
                'formatted' => $this->formatted_price_usd
            ],
            'inr' => [
                'raw' => $this->price_inr,
                'formatted' => $this->formatted_price_inr
            ]
        ];
    }

    /**
     * Get price range for product
     */
    public function getPriceRangeAttribute(): array
    {
        $tiers = $this->pricingTiers;
        
        if ($tiers->isEmpty()) {
            return [
                'min' => null,
                'max' => null,
                'formatted' => 'Contact for pricing'
            ];
        }

        $minInr = $tiers->min('price_inr');
        $maxInr = $tiers->max('price_inr');
        $minUsd = $tiers->min('price_usd');
        $maxUsd = $tiers->max('price_usd');

        return [
            'inr' => [
                'min' => $minInr,
                'max' => $maxInr,
                'formatted_min' => $minInr ? '₹' . number_format($minInr, 2) : null,
                'formatted_max' => $maxInr ? '₹' . number_format($maxInr, 2) : null,
            ],
            'usd' => [
                'min' => $minUsd,
                'max' => $maxUsd,
                'formatted_min' => $minUsd ? '$' . number_format($minUsd, 2) : null,
                'formatted_max' => $maxUsd ? '$' . number_format($maxUsd, 2) : null,
            ]
        ];
    }

    /**
     * Get primary image URL
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primary = $this->primaryImage; // 👈 Ab yeh HasOne relationship hai
        return $primary ? $primary->image_url : asset('images/default-product.png');
    }

    /**
     * Get all images as array
     */
    public function getImageUrlsAttribute(): array
    {
        return $this->images->map(function($image) {
            return [
                'id' => $image->id,
                'url' => $image->image_url,
                'thumbnail' => $image->thumbnail_url,
                'alt_text' => $image->alt_text,
                'is_primary' => $image->is_primary
            ];
        })->toArray();
    }

    /**
     * Get related products as array
     */
    public function getRelatedProductsListAttribute(): array
    {
        return $this->relatedProducts->map(function($relation) {
            return [
                'id' => $relation->relatedProduct->id,
                'title' => $relation->relatedProduct->title,
                'slug' => $relation->relatedProduct->slug,
                'type' => $relation->relationship_type,
                'type_label' => ucfirst($relation->relationship_type),
                'display_order' => $relation->display_order
            ];
        })->toArray();
    }

    /**
     * Get excerpt of short description
     */
    public function getExcerptAttribute($length = 100): string
    {
        if (!$this->short_description) {
            return '';
        }
        
        return strlen($this->short_description) > $length 
            ? substr($this->short_description, 0, $length) . '...' 
            : $this->short_description;
    }

    // ============ MUTATORS ============

    /**
     * Set the image URL with full path
     */
    public function setImageUrlAttribute($value): void
    {
        if ($value && !str_starts_with($value, 'http') && !str_starts_with($value, 'storage/')) {
            $value = 'storage/' . ltrim($value, '/');
        }
        $this->attributes['image_url'] = $value;
    }

    /**
     * Set the video URL with full path
     */
    public function setVideoUrlAttribute($value): void
    {
        if ($value && !str_starts_with($value, 'http') && !str_starts_with($value, 'storage/')) {
            $value = 'storage/' . ltrim($value, '/');
        }
        $this->attributes['video_url'] = $value;
    }

    // ============ SCOPES ============

    /**
     * Scope a query to only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')
                     ->orderBy('title', 'asc');
    }

    /**
     * Scope a query to search products
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('short_description', 'LIKE', "%{$search}%")
              ->orWhere('full_description', 'LIKE', "%{$search}%")
              ->orWhere('slug', 'LIKE', "%{$search}%")
              ->orWhere('meta_keywords', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by price range
     */
    public function scopePriceBetween($query, $min, $max, $currency = 'inr')
    {
        $column = $currency === 'usd' ? 'price_usd' : 'price_inr';
        return $query->whereBetween($column, [$min, $max]);
    }

    // ============ BOOT METHODS ============

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = str()->slug($product->title);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('title') && !$product->isDirty('slug')) {
                $product->slug = str()->slug($product->title);
            }
        });

        static::deleting(function ($product) {
            $product->images()->delete();
            $product->features()->delete();
            $product->pricingTiers()->delete();
            $product->relatedProducts()->delete();
        });
    }

    // ============ HELPER METHODS ============

    /**
     * Check if product is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Toggle active status
     */
    public function toggleActive(): bool
    {
        $this->is_active = !$this->is_active;
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