<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    
    protected $fillable = [
        'category_name'
    ];

    // Remove all relationship methods since they don't exist in your table
    
    // Simple scope for ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('category_name');
    }
}