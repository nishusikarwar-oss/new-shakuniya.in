<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'full_name',
        'email',
        'phone',
        'gender',
        'message',
        'cv_file',
        'created_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the CV file URL.
     *
     * @return string|null
     */
    public function getCvFileUrlAttribute()
    {
        if ($this->cv_file) {
            return asset('storage/' . $this->cv_file);
        }
        return null;
    }

    /**
     * Get the job that owns the application.
     */
    public function job()
    {
        return $this->belongsTo(JobOpening::class, 'job_id');
    }
}