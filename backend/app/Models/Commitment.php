<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commitment extends Model
{
    use HasFactory;

    protected $primaryKey = 'commitment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'commitments';

    public $timestamps = false; // Only created_at exists, no updated_at

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'icon_name',
        'icon_url',
        'display_order',
        'is_active'
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
     * Get the company that owns the commitment
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Get the full icon URL
     */
    public function getIconUrlAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }

    /**
     * Scope a query to only active commitments
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