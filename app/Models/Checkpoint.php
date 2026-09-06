<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checkpoint extends Model
{
    use HasFactory;

    protected $table = 'checkpoints';

    protected $fillable = [
        'rt',
        'nama_titik',
        'kode_qr',
        'latitude',
        'longitude',
        'deskripsi',
        'urutan_patroli',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'urutan_patroli' => 'integer',
    ];

    public function presensis(): HasMany
    {
        return $this->hasMany(PresensiRonda::class, 'checkpoint_id');
    }
}
