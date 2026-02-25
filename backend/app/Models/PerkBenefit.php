<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerkBenefit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'perks_benefits';

    public $timestamps = false; // Only created_at exists, no updated_at

    protected $fillable = [
        'title',
        'description',
        'icon_image',
        'icon_name',
        'display_order',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'display_order' => 0,
        'category' => 'work_life'
    ];

    const CATEGORIES = [
        'health' => 'Health & Wellness',
        'financial' => 'Financial Benefits',
        'work_life' => 'Work-Life Balance',
        'growth' => 'Growth & Development',
        'culture' => 'Culture & Perks'
    ];

    const CATEGORY_COLORS = [
        'health' => 'green',
        'financial' => 'blue',
        'work_life' => 'purple',
        'growth' => 'orange',
        'culture' => 'pink'
    ];

    const CATEGORY_ICONS = [
        'health' => 'heart-pulse',
        'financial' => 'banknote',
        'work_life' => 'calendar-clock',
        'growth' => 'trending-up',
        'culture' => 'users'
    ];

    /**
     * Get the full icon image URL
     */
    public function getIconImageAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }

    /**
     * Get icon to display (image if exists, otherwise icon name)
     */
    public function getDisplayIconAttribute(): string
    {
        if ($this->icon_image) {
            return $this->icon_image;
        }
        
        if ($this->icon_name) {
            return $this->icon_name;
        }
        
        // Fallback to category icon
        return self::CATEGORY_ICONS[$this->category] ?? 'gift';
    }

    /**
     * Get icon type (image or icon)
     */
    public function getIconTypeAttribute(): string
    {
        if ($this->icon_image) {
            return 'image';
        }
        
        if ($this->icon_name) {
            return 'icon';
        }
        
        return 'icon'; // default to icon
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get category color
     */
    public function getCategoryColorAttribute(): string
    {
        return self::CATEGORY_COLORS[$this->category] ?? 'gray';
    }

    /**
     * Get formatted display order with leading zero
     */
    public function getFormattedOrderAttribute(): string
    {
        return str_pad($this->display_order, 2, '0', STR_PAD_LEFT);
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
     * Scope a query to only active perks
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by display order
     */
    public function scopeOrdered($query)  // <-- यह scope method add किया
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
     * Scope a query to search perks
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get perks grouped by category
     */
    public static function getGroupedByCategory()
    {
        $result = [];
        
        foreach (array_keys(self::CATEGORIES) as $category) {
            $perks = self::active()
                ->inCategory($category)
                ->ordered()  // <-- यहाँ use हो रहा है
                ->get();
            
            if ($perks->isNotEmpty()) {
                $result[$category] = [
                    'name' => self::CATEGORIES[$category],
                    'color' => self::CATEGORY_COLORS[$category],
                    'icon' => self::CATEGORY_ICONS[$category],
                    'perks' => $perks
                ];
            }
        }
        
        return $result;
    }
}