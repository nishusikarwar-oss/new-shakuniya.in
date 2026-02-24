<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessStep extends Model
{
    use HasFactory;

    protected $primaryKey = 'step_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'process_steps';

    public $timestamps = false; // Only created_at exists, no updated_at

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'icon_name',
        'icon_url',
        'step_number',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'step_number' => 'integer',
        'company_id' => 'integer',
        'created_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'company_id' => 1
    ];

    /**
     * Get the company that owns the process step
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
     * Scope a query to only active steps
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by step number
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step_number', 'asc');
    }

    /**
     * Get formatted step number with leading zero
     */
    public function getFormattedStepNumberAttribute(): string
    {
        return str_pad($this->step_number, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get step with prefix
     */
    public function getStepWithPrefixAttribute(): string
    {
        return 'Step ' . $this->formatted_step_number;
    }
}