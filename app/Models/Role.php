<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'icon',
        'badge_color',
    ];

    /**
     * Relasi ke Users yang memiliki peran ini
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
