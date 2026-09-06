<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiRonda extends Model
{
    use HasFactory;

    protected $table = 'presensi_ronda';

    protected $fillable = [
        'user_id',
        'checkpoint_id',
        'waktu_scan',
        'latitude',
        'longitude',
        'foto_kondisi',
        'catatan',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class, 'checkpoint_id');
    }
}
