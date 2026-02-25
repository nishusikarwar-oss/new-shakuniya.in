<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'products';

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
        'display_order' => 0
    ];
    /**
 * Get the pricing tiers for this product
 */
public function pricingTiers()
{
    return $this->hasMany(ProductPricingTier::class, 'product_id', 'id')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all pricing tiers (including inactive)
 */
public function allPricingTiers()
{
    return $this->hasMany(ProductPricingTier::class, 'product_id', 'id')
                ->orderBy('display_order', 'asc');
}

/**
 * Get popular pricing tier
 */
public function popularTier()
{
    return $this->hasOne(ProductPricingTier::class, 'product_id', 'id')
                ->where('is_popular', true)
                ->where('is_active', true);
}

/**
 * Get pricing by billing period
 */
public function getPricingByPeriod($period)
{
    return $this->pricingTiers()
                ->where('billing_period', $period)
                ->get();
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
}/**
 * Get related products (where this product is the main product)
 */
public function relatedProducts()
{
    return $this->hasMany(RelatedProduct::class, 'product_id', 'id')
                ->with('relatedProduct')
                ->ordered();
}

/**
 * Get products that have this as related (reverse relation)
 */
public function relatedFrom()
{
    return $this->hasMany(RelatedProduct::class, 'related_product_id', 'id')
                ->with('product')
                ->ordered();
}

/**
 * Get upsell products
 */
public function upsells()
{
    return $this->relatedProducts()->where('relationship_type', 'upsell');
}

/**
 * Get cross-sell products
 */
public function crossSells()
{
    return $this->relatedProducts()->where('relationship_type', 'cross-sell');
}

/**
 * Get alternative products
 */
public function alternatives()
{
    return $this->relatedProducts()->where('relationship_type', 'alternative');
}

/**
 * Get all related products as array
 */
public function getRelatedProductsListAttribute(): array
{
    return $this->relatedProducts->map(function($relation) {
        return [
            'id' => $relation->relatedProduct->id,
            'title' => $relation->relatedProduct->title,
            'slug' => $relation->relatedProduct->slug,
            'type' => $relation->relationship_type,
            'type_label' => $relation->relationship_type_label,
            'display_order' => $relation->display_order
        ];
    })->toArray();
}
    /**
 * Get the images for this product
 */
public function images()
{
    return $this->hasMany(ProductImage::class, 'product_id', 'id')
                ->orderBy('display_order', 'asc');
}

/**
 * Get the primary image for this product
 */
public function primaryImage()
{
    return $this->hasOne(ProductImage::class, 'product_id', 'id')
                ->where('is_primary', true);
}

/**
 * Get primary image URL
 */
public function getPrimaryImageUrlAttribute(): ?string
{
    $primary = $this->primaryImage;
    return $primary ? $primary->image_url : asset('images/default-product.png');
}

/**
 * Get all images as array of URLs
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
            'usd' => $this->formatted_price_usd,
            'inr' => $this->formatted_price_inr,
            'usd_raw' => $this->price_usd,
            'inr_raw' => $this->price_inr
        ];
    }
    /**
 * Get the features for this product
 */
public function features()
{
    return $this->hasMany(ProductFeature::class, 'product_id', 'id')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all features (including inactive)
 */
public function allFeatures()
{
    return $this->hasMany(ProductFeature::class, 'product_id', 'id')
                ->orderBy('display_order', 'asc');
}

    /**
     * Get image URL with full path
     */
    public function getImageUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }

    /**
     * Get video URL with full path
     */
    public function getVideoUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return asset('storage/' . $value);
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

    /**
     * Generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = str()->slug($product->title);
            }
            
            if (auth()->check() && empty($product->created_by)) {
                $product->created_by = auth()->id();
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('title') && !$product->isDirty('slug')) {
                $product->slug = str()->slug($product->title);
            }
            
            if (auth()->check()) {
                $product->updated_by = auth()->id();
            }
        });
    }

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
        return $query->orderBy('display_order', 'asc');
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
              ->orWhere('slug', 'LIKE', "%{$search}%");
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
}