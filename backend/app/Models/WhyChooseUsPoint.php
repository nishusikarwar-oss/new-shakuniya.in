<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhyChooseUsPoint extends Model
{
    use HasFactory;

    protected $primaryKey = 'point_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'why_choose_us_points';

    public $timestamps = false; // Only created_at exists, no updated_at

    protected $fillable = [
        'company_id',
        'point_text',
        'display_order',
        'is_active',
        'service_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'company_id' => 'integer',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'display_order' => 0,
        'company_id' => 1
    ];

    /**
     * Get the company that owns the point
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Get truncated point text
     */
    public function getShortTextAttribute($length = 100): string
    {
        return strlen($this->point_text) > $length 
            ? substr($this->point_text, 0, $length) . '...' 
            : $this->point_text;
    }

    /**
     * Scope a query to only active points
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
}