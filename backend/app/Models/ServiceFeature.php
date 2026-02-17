<?php
// app/Models/ServiceFeature.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFeature extends Model
{
    protected $fillable = [
        'service_id',
        'feature'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}