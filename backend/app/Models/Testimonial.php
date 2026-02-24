<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $primaryKey = 'testimonial_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'testimonials';

    protected $fillable = [
        'company_id',
        'client_name',
        'client_position',
        'client_company',
        'client_image',
        'testimonial_text',
        'rating',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'company_id' => 'integer',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'display_order' => 0,
        'company_id' => 1,
        'rating' => 5
    ];

    /**
     * Get the company that owns the testimonial
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Get the full client image URL
     */
    public function getClientImageAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value ?? asset('images/default-client.png');
    }

    /**
     * Get client full info (name with company)
     */
    public function getClientFullInfoAttribute(): string
    {
        $info = $this->client_name;
        
        if ($this->client_position) {
            $info .= ', ' . $this->client_position;
        }
        
        if ($this->client_company) {
            $info .= ' at ' . $this->client_company;
        }
        
        return $info;
    }

    /**
     * Get rating stars as HTML
     */
    public function getRatingStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }

    /**
     * Get rating percentage
     */
    public function getRatingPercentageAttribute(): int
    {
        return ($this->rating / 5) * 100;
    }

    /**
     * Get truncated testimonial text
     */
    public function getExcerptAttribute($length = 150): string
    {
        return strlen($this->testimonial_text) > $length 
            ? substr($this->testimonial_text, 0, $length) . '...' 
            : $this->testimonial_text;
    }

    /**
     * Scope a query to only active testimonials
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
     * Scope a query to filter by minimum rating
     */
    public function scopeMinRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope a query to search by client name or company
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('client_name', 'LIKE', "%{$search}%")
              ->orWhere('client_company', 'LIKE', "%{$search}%")
              ->orWhere('testimonial_text', 'LIKE', "%{$search}%");
        });
    }
}