<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perk extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'perks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'icon',
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
     * Get the icon URL.
     *
     * @return string|null
     */
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return asset('storage/perks/' . $this->icon);
        }
        return null;
    }

    /**
     * Get the icon path.
     *
     * @return string|null
     */
    public function getIconPathAttribute()
    {
        if ($this->icon) {
            return 'perks/' . $this->icon;
        }
        return null;
    }
}