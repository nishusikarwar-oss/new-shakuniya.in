<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model\Employees;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true
    ];

    /**
     * Get the employees for this department
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }

    /**
     * Get the team members in this department
     */
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class, 'department_id', 'id');
    }

    /**
     * Get active employees count
     */
    public function getActiveEmployeesCountAttribute()
    {
        return $this->employees()->where('is_active', true)->count();
    }

    /**
     * Get total employees count
     */
    public function getTotalEmployeesCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * Get icon class with prefix
     */
    public function getIconClassAttribute()
    {
        return $this->icon_name ? 'bi bi-' . $this->icon_name : 'bi bi-folder';
    }

    /**
     * Generate slug from name
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($department) {
            if (empty($department->slug)) {
                $department->slug = str()->slug($department->name);
            }
        });

        static::updating(function ($department) {
            if ($department->isDirty('name') && !$department->isDirty('slug')) {
                $department->slug = str()->slug($department->name);
            }
        });
    }

    /**
     * Scope a query to only active departments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search departments
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }
}