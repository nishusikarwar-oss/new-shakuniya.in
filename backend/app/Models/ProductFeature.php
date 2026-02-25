<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFeature extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'product_features';

    protected $fillable = [
        'product_id',
        'icon_name',
        'title',
        'description',
        'display_order',
        'is_active'
    ];

    protected $casts = [
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
     * Icon mapping for Lucide/Feather icons
     */
    const ICONS = [
        'Zap' => 'Zap',
        'MessageSquare' => 'MessageSquare',
        'Users' => 'Users',
        'ShieldCheck' => 'ShieldCheck',
        'Heart' => 'Heart',
        'Star' => 'Star',
        'Globe' => 'Globe',
        'Clock' => 'Clock',
        'Award' => 'Award',
        'Target' => 'Target',
        'TrendingUp' => 'TrendingUp',
        'Smartphone' => 'Smartphone',
        'Laptop' => 'Laptop',
        'Database' => 'Database',
        'Cloud' => 'Cloud',
        'Lock' => 'Lock',
        'Mail' => 'Mail',
        'Phone' => 'Phone',
        'Camera' => 'Camera',
        'Video' => 'Video'
    ];

    /**
     * Get the product that owns this feature
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get icon component name
     */
    public function getIconComponentAttribute(): string
    {
        return $this->icon_name ?? 'Circle';
    }

    /**
     * Get icon with proper formatting
     */
    public function getIconAttribute(): array
    {
        return [
            'name' => $this->icon_name,
            'component' => $this->icon_component,
            'library' => 'lucide' // or 'feather'
        ];
    }

    /**
     * Get excerpt of description
     */
    public function getExcerptAttribute($length = 100): string
    {
        if (!$this->description) {
            return '';
        }
        
        return strlen($this->description) > $length 
            ? substr($this->description, 0, $length) . '...' 
            : $this->description;
    }

    /**
     * Scope a query to only active features
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
     * Scope a query to filter by product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Get all available icons
     */
    public static function getAvailableIcons(): array
    {
        return array_keys(self::ICONS);
    }

    /**
     * Get icons as options for dropdown
     */
    public static function getIconOptions(): array
    {
        $options = [];
        foreach (self::ICONS as $key => $value) {
            $options[] = [
                'value' => $key,
                'label' => $key,
                'component' => $value
            ];
        }
        return $options;
    }
}