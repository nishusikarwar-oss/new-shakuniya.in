<?php
// app/Models/ActivityLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // Tell Laravel this table exists in phpMyAdmin
    protected $table = 'activity_logs';
    
    // Specify which columns can be mass assigned
    protected $fillable = [
        'action',
        'description',
        'subject_type',
        'subject_id',
        'user_id',
        'user_name',
        'ip_address',
        'user_agent',
        'properties',
        'log_type',
        'count',
        'percentage_change'
    ];

    // Specify column types for casting
    protected $casts = [
        'properties' => 'array',
        'count' => 'integer',
        'percentage_change' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Polymorphic relationship
    public function subject()
    {
        return $this->morphTo();
    }
}