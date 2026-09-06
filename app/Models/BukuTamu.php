<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuTamu extends Model
{
    use HasFactory;

    protected $table = 'buku_tamu';

    protected $fillable = [
        'nama_tamu',
        'nik',
        'no_hp',
        'alamat_asal',
        'tujuan_kunjungan',
        'warga_yang_dikunjungi',
        'rt',
        'tanggal_tiba',
        'tanggal_keluar',
        'status',
        'foto_identitas',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_tiba' => 'datetime',
        'tanggal_keluar' => 'datetime',
    ];

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi RT',
            'disetujui' => 'Tamu Terverifikasi',
            'selesai' => 'Kunjungan Selesai',
            default => 'Tercatat',
        };
    }
}
