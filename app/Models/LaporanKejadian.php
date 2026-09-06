<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKejadian extends Model
{
    use HasFactory;

    protected $table = 'laporan_kejadian';

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'foto',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Tindakan',
            'diproses' => 'Sedang Ditangani',
            'selesai' => 'Selesai / Teratasi',
            default => 'Laporan Diterima',
        };
    }
}
