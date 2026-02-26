<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory;

    protected $primaryKey = 'member_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'team_members';

    protected $fillable = [
        'company_id',
        'name',
        'position',
        'bio',
        'profile_image',
        'linkedin_url',
        'twitter_url',
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
     * Get the company that owns the team member
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Get the full profile image URL
     */
    public function getProfileImageAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }

    /**
     * Get default avatar if no profile image
     */
    public function getAvatarAttribute(): string
    {
        return $this->profile_image ?? asset('images/default-avatar.png');
    }

    /**
     * Get initials from name
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        return substr($initials, 0, 2);
    }

    /**
     * Get formatted name with position
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ($this->position ? ' - ' . $this->position : '');
    }

    /**
     * Check if member has social links
     */
    public function getHasSocialLinksAttribute(): bool
    {
        return !empty($this->linkedin_url) || !empty($this->twitter_url);
    }

    /**
     * Scope a query to only active members
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
     * Scope a query to search by name or position
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('position', 'LIKE', "%{$search}%")
              ->orWhere('bio', 'LIKE', "%{$search}%");
        });
    }
}