<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TierFeature extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'tier_features';

    public $timestamps = false; // Only created_at exists

    protected $fillable = [
        'tier_id',
        'feature_description',
        'is_available',
        'display_order'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_available' => true,
        'display_order' => 0
    ];

    /**
     * Get the pricing tier that owns this feature
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(ProductPricingTier::class, 'tier_id', 'id');
    }

    /**
     * Get the product through tier
     */
    public function getProductAttribute()
    {
        return $this->tier?->product;
    }

    /**
     * Get icon based on availability
     */
    public function getAvailabilityIconAttribute(): string
    {
        return $this->is_available ? 'CheckCircle' : 'XCircle';
    }

    /**
     * Get color based on availability
     */
    public function getAvailabilityColorAttribute(): string
    {
        return $this->is_available ? 'green' : 'red';
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_available) {
            return '<span class="badge bg-success">Available</span>';
        }
        return '<span class="badge bg-secondary">Not Available</span>';
    }

    /**
     * Get excerpt of feature description
     */
    public function getExcerptAttribute($length = 100): string
    {
        if (!$this->feature_description) {
            return '';
        }
        
        return strlen($this->feature_description) > $length 
            ? substr($this->feature_description, 0, $length) . '...' 
            : $this->feature_description;
    }

    /**
     * Scope a query to only available features
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Scope a query to filter by tier
     */
    public function scopeForTier($query, $tierId)
    {
        return $query->where('tier_id', $tierId);
    }
}