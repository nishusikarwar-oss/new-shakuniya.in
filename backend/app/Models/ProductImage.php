<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'product_images';

    public $timestamps = false; // Only created_at exists

    protected $fillable = [
        'product_id',
        'image_url',
        'alt_text',
        'is_primary',
        'display_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_primary' => false,
        'display_order' => 0
    ];

    /**
     * Get the product that owns this image
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute($value): string
    {
        if (!$value) return asset('images/default-product.png');
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        // Remove 'storage/' prefix if it exists in DB to avoid double prefixing
        $path = ltrim($value, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        
        return asset('storage/' . $path);
    }

    /**
     * Get thumbnail URL (you can modify size as needed)
     */
    public function getThumbnailUrlAttribute(): string
    {
        // You can implement thumbnail generation logic here
        // For now, return the same image
        return $this->image_url;
    }

    /**
     * Get medium size image URL
     */
    public function getMediumUrlAttribute(): string
    {
        // You can implement medium size image logic here
        return $this->image_url;
    }

    /**
     * Get all image sizes
     */
    public function getSizesAttribute(): array
    {
        return [
            'thumbnail' => $this->thumbnail_url,
            'medium' => $this->medium_url,
            'original' => $this->image_url
        ];
    }

    /**
     * Scope a query to only primary images
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Scope a query to filter by product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Set as primary image (and remove other primaries)
     */
    public function setAsPrimary(): void
    {
        // Remove primary from other images of same product
        self::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
        
        // Set this as primary
        $this->is_primary = true;
        $this->save();
    }
}