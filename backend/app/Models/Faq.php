<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'answer',
        'status'
    ];

    protected $casts = [
        'status' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the active status attribute.
     */
    public function getIsActiveAttribute(): bool
    {
        return !is_null($this->status);
    }

    /**
     * Scope a query to only include active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('status');
    }

    /**
     * Scope a query to only include inactive FAQs.
     */
    public function scopeInactive($query)
    {
        return $query->whereNull('status');
    }

    /**
     * Scope a query to search FAQs.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('question', 'LIKE', "%{$search}%")
              ->orWhere('answer', 'LIKE', "%{$search}%");
        });
    }
}