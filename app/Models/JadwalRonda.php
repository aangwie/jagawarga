<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalRonda extends Model
{
    use HasFactory;

    protected $table = 'jadwal_ronda';

    protected $fillable = [
        'user_id',
        'hari',
        'rt_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
