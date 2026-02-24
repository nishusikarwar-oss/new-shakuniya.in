<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Statistic extends Model
{
    use HasFactory;

    protected $primaryKey = 'stat_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'statistics';

    protected $fillable = [
        'company_id',
        'label',
        'value',
        'suffix',
        'prefix',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'value' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'company_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'display_order' => 0,
        'company_id' => 1
    ];

    /**
     * Get the company that owns the statistic
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Get formatted value with prefix and suffix
     */
    public function getFormattedValueAttribute(): string
    {
        $formatted = '';
        
        if ($this->prefix) {
            $formatted .= $this->prefix;
        }
        
        $formatted .= number_format($this->value);
        
        if ($this->suffix) {
            $formatted .= $this->suffix;
        }
        
        return $formatted;
    }

    /**
     * Get value with K/M/B formatting for large numbers
     */
    public function getShortValueAttribute(): string
    {
        $value = $this->value;
        
        if ($value >= 1000000000) {
            return round($value / 1000000000, 1) . 'B';
        }
        
        if ($value >= 1000000) {
            return round($value / 1000000, 1) . 'M';
        }
        
        if ($value >= 1000) {
            return round($value / 1000, 1) . 'K';
        }
        
        return (string) $value;
    }

    /**
     * Get full formatted short value with prefix/suffix
     */
    public function getFormattedShortValueAttribute(): string
    {
        $formatted = '';
        
        if ($this->prefix) {
            $formatted .= $this->prefix;
        }
        
        $formatted .= $this->short_value;
        
        if ($this->suffix) {
            $formatted .= $this->suffix;
        }
        
        return $formatted;
    }

    /**
     * Scope a query to only active statistics
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