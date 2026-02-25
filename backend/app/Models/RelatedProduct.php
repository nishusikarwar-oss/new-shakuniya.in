<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelatedProduct extends Model
{
    use HasFactory;

    protected $table = 'related_products';

    public $incrementing = false;
    public $timestamps = false; // Only created_at exists

    protected $primaryKey = null; // Composite key

    protected $fillable = [
        'product_id',
        'related_product_id',
        'relationship_type',
        'display_order'
    ];

    protected $casts = [
        'display_order' => 'integer',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'relationship_type' => 'cross-sell',
        'display_order' => 0
    ];

    /**
     * Relationship types
     */
    const RELATIONSHIP_TYPES = [
        'upsell' => 'Upsell',
        'cross-sell' => 'Cross Sell',
        'alternative' => 'Alternative'
    ];

    /**
     * Relationship type colors
     */
    const RELATIONSHIP_COLORS = [
        'upsell' => 'success',
        'cross-sell' => 'primary',
        'alternative' => 'warning'
    ];

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get the related product
     */
    public function relatedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'related_product_id', 'id');
    }

    /**
     * Get relationship type label
     */
    public function getRelationshipTypeLabelAttribute(): string
    {
        return self::RELATIONSHIP_TYPES[$this->relationship_type] ?? ucfirst($this->relationship_type);
    }

    /**
     * Get relationship type color
     */
    public function getRelationshipTypeColorAttribute(): string
    {
        return self::RELATIONSHIP_COLORS[$this->relationship_type] ?? 'secondary';
    }

    /**
     * Get relationship badge
     */
    public function getRelationshipBadgeAttribute(): string
    {
        $color = $this->relationship_type_color;
        $label = $this->relationship_type_label;
        
        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    /**
     * Scope a query to filter by relationship type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('relationship_type', $type);
    }

    /**
     * Scope a query to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Get related products for a specific product
     */
    public static function getForProduct($productId, $type = null)
    {
        $query = self::with('relatedProduct')
            ->where('product_id', $productId)
            ->ordered();

        if ($type) {
            $query->ofType($type);
        }

        return $query->get()->map(function($item) {
            return [
                'id' => $item->relatedProduct->id,
                'title' => $item->relatedProduct->title,
                'slug' => $item->relatedProduct->slug,
                'price' => $item->relatedProduct->prices,
                'primary_image' => $item->relatedProduct->primary_image_url,
                'relationship_type' => $item->relationship_type,
                'relationship_label' => $item->relationship_type_label,
                'relationship_color' => $item->relationship_type_color,
                'relationship_badge' => $item->relationship_badge,
                'display_order' => $item->display_order
            ];
        });
    }

    /**
     * Get all relationship types for dropdown
     */
    public static function getRelationshipTypes(): array
    {
        $types = [];
        foreach (self::RELATIONSHIP_TYPES as $value => $label) {
            $types[] = [
                'value' => $value,
                'label' => $label,
                'color' => self::RELATIONSHIP_COLORS[$value] ?? 'secondary'
            ];
        }
        return $types;
    }
}