<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'setting_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'site_settings';

    public $timestamps = false; // Only updated_at exists, no created_at

    protected $fillable = [
        'company_id',
        'setting_key',
        'setting_value',
        'description'
    ];

    protected $casts = [
        'setting_value' => 'array', // Automatically cast JSON to array
        'company_id' => 'integer'
    ];

    protected $attributes = [
        'company_id' => 1
    ];

    /**
     * Get the company that owns the setting
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Get setting value as object
     */
    public function getValueAttribute()
    {
        return $this->setting_value;
    }

    /**
     * Get specific nested value using dot notation
     */
    public function getNestedValue(string $key, $default = null)
    {
        $value = $this->setting_value;
        
        foreach (explode('.', $key) as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        
        return $value;
    }

    /**
     * Set nested value using dot notation
     */
    public function setNestedValue(string $key, $value): void
    {
        $data = $this->setting_value ?? [];
        $keys = explode('.', $key);
        $lastKey = array_pop($keys);
        
        $temp = &$data;
        foreach ($keys as $k) {
            if (!isset($temp[$k]) || !is_array($temp[$k])) {
                $temp[$k] = [];
            }
            $temp = &$temp[$k];
        }
        
        $temp[$lastKey] = $value;
        $this->setting_value = $data;
    }

    /**
     * Scope a query to get setting by key
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('setting_key', $key);
    }

    /**
     * Scope a query to get settings by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}