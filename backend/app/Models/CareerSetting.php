<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'career_settings';

    public $timestamps = false; // Only updated_at exists

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description'
    ];

    protected $casts = [
        'setting_value' => 'array', // Automatically cast JSON to array
        'updated_at' => 'datetime'
    ];

    /**
     * Get setting value as object/array
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
     * Get all settings as key-value pairs
     */
    public static function getAllAsArray(): array
    {
        $settings = [];
        $records = self::all();
        
        foreach ($records as $record) {
            $settings[$record->setting_key] = $record->setting_value;
        }
        
        return $settings;
    }

    /**
     * Get a setting value by key with default
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::byKey($key)->first();
        
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, $value, ?string $description = null): self
    {
        $setting = self::byKey($key)->first();
        
        if ($setting) {
            $setting->setting_value = $value;
            if ($description) {
                $setting->description = $description;
            }
            $setting->save();
        } else {
            $setting = self::create([
                'setting_key' => $key,
                'setting_value' => $value,
                'description' => $description
            ]);
        }
        
        return $setting;
    }

    /**
     * Get career page settings with defaults
     */
    public static function getCareerPageSettings(): array
    {
        $defaults = [
            'page_title' => 'Career Opportunities',
            'page_subtitle' => 'Join our team and build your future with us',
            'meta_title' => 'Careers - Join Our Team',
            'meta_description' => 'Explore exciting career opportunities and join our dynamic team',
            'header_image' => null,
            'show_stats' => true,
            'show_perks' => true,
            'show_testimonials' => true,
            'application_form' => [
                'allow_cover_letter' => true,
                'allow_portfolio' => true,
                'allow_linkedin' => true,
                'require_phone' => true,
                'max_file_size' => 5, // MB
                'allowed_file_types' => ['pdf', 'doc', 'docx']
            ],
            'email_notifications' => [
                'applicant_confirmation' => true,
                'admin_notification' => true,
                'status_change' => true
            ],
            'social_share' => [
                'linkedin' => true,
                'twitter' => true,
                'facebook' => false
            ]
        ];

        $settings = self::getAllAsArray();
        
        return array_merge($defaults, $settings);
    }
}