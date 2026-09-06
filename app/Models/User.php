<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nik',
        'phone',
        'email',
        'password',
        'role',
        'rt_id',
        'rw_id',
        'alamat',
        'no_rumah',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Panic Alerts (Kentongan Darurat)
     */
    public function panicAlerts(): HasMany
    {
        return $this->hasMany(PanicAlert::class);
    }

    /**
     * Relasi ke Laporan Kejadian Warga
     */
    public function laporanKejadians(): HasMany
    {
        return $this->hasMany(LaporanKejadian::class);
    }

    /**
     * Relasi ke Jadwal Ronda
     */
    public function jadwalRondas(): HasMany
    {
        return $this->hasMany(JadwalRonda::class);
    }

    /**
     * Relasi ke Presensi Ronda (scan checkpoint)
     */
    public function presensiRondas(): HasMany
    {
        return $this->hasMany(PresensiRonda::class);
    }

    /**
     * Helper Role Checking
     */
    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    public function isPetugasRonda(): bool
    {
        return $this->role === 'petugas_ronda';
    }

    public function isRt(): bool
    {
        return $this->role === 'rt';
    }

    public function isRw(): bool
    {
        return $this->role === 'rw';
    }

    public function isBhabinkamtibmas(): bool
    {
        return $this->role === 'bhabinkamtibmas';
    }

    public function isPengurus(): bool
    {
        return in_array($this->role, ['rt', 'rw', 'bhabinkamtibmas']);
    }

    public function getRoleBadgeAttribute(): string
    {
        return match($this->role) {
            'warga' => 'Warga Lingkungan',
            'petugas_ronda' => 'Petugas Ronda',
            'rt' => 'Ketua RT ' . ($this->rt_id ?? '01'),
            'rw' => 'Pengurus RW ' . ($this->rw_id ?? '02'),
            'bhabinkamtibmas' => 'Bhabinkamtibmas',
            default => 'Pengguna'
        };
    }
}
