<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorysModel extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'categorie';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'image_url',
        'parent_id',
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

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CategorysModel::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CategorysModel::class, 'parent_id', 'id')
                    ->where('is_active', true)
                    ->orderBy('display_order', 'asc');
    }

    // Scopes
    public function scopeActive($query) 
    { 
        return $query->where('is_active', true); 
    }
    
    public function scopeRoot($query) 
    { 
        return $query->whereNull('parent_id'); 
    }
    
    public function scopeOrdered($query) 
    { 
        return $query->orderBy('display_order', 'asc'); 
    }
}