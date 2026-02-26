<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFeature extends Model
{
    use HasFactory;

    protected $primaryKey = 'feature_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'service_features';

    public $timestamps = false; // Only created_at exists, no updated_at

    protected $fillable = [
        'service_id',
        'title',
        'description',
        'icon_name',
        'icon_url',
        'image_url',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'service_id' => 'integer',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'display_order' => 0
    ];

    /**
     * Get the service that owns the feature
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
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
     * Get the full image URL
     */
    public function getImageUrlAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }
}