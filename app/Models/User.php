<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

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
        'is_nik_verified',
        'nik_verified_at',
        'phone',
        'nama_ibu',
        'email',
        'password',
        'role',
        'rt_id',
        'rw_id',
        'alamat',
        'no_rumah',
        'registered_device_id',
        'device_info',
        'device_registered_at',
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
            'device_registered_at' => 'datetime',
            'is_nik_verified' => 'boolean',
            'nik_verified_at' => 'datetime',
        ];
    }

    /**
     * Cek apakah NIK pengguna telah terverifikasi sah oleh pengurus RW/RT
     */
    public function isNikVerified(): bool
    {
        return !empty($this->nik) && (bool)($this->is_nik_verified ?? false);
    }

    /**
     * Relasi Many-to-Many ke Roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
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
     * Relasi ke Permohonan Ganti / Reset Perangkat
     */
    public function deviceResetRequests(): HasMany
    {
        return $this->hasMany(DeviceResetRequest::class);
    }

    /**
     * Mengambil peran aktif saat ini dalam sesi atau peran utama
     */
    public function currentRole(): string
    {
        if (Auth::check() && Auth::id() === $this->id && session()->has('active_role')) {
            return session('active_role');
        }

        return $this->attributes['role'] ?? 'warga';
    }

    /**
     * Accessor dinamis untuk $user->role agar kode yang telah ada tetap bekerja
     */
    public function getRoleAttribute($value): string
    {
        if (Auth::check() && Auth::id() === $this->id && session()->has('active_role')) {
            return session('active_role');
        }

        return $value ?? 'warga';
    }

    /**
     * Cek apakah user memiliki peran spesifik (dari relasi roles atau kolom role)
     */
    public function hasRole(string $role): bool
    {
        if (($this->attributes['role'] ?? null) === $role) {
            return true;
        }

        // Cek dalam koleksi roles
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('name', $role);
        }

        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Cek apakah user memiliki salah satu peran dalam array
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $r) {
            if ($this->hasRole($r)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sinkronkan peran pengguna ke tabel pivot role_user
     */
    public function syncRoles(array $roleNames): void
    {
        $roles = Role::whereIn('name', $roleNames)->get();
        $this->roles()->sync($roles->pluck('id')->toArray());

        if (!empty($roleNames)) {
            $this->attributes['role'] = $roleNames[0];
            $this->saveQuietly();
        }
    }

    /**
     * Cek status apakah perangkat warga sudah terdaftar
     */
    public function isDeviceRegistered(): bool
    {
        return !empty($this->registered_device_id);
    }

    /**
     * Reset ikatan perangkat akun
     */
    public function resetDevice(): void
    {
        $this->update([
            'registered_device_id' => null,
            'device_info' => null,
            'device_registered_at' => null,
        ]);
    }

    /**
     * Helper Role Checking berdasarkan peran aktif saat ini
     */
    public function isWarga(): bool
    {
        return $this->currentRole() === 'warga';
    }

    public function isPetugasRonda(): bool
    {
        return $this->currentRole() === 'petugas_ronda';
    }

    public function isRt(): bool
    {
        return $this->currentRole() === 'rt';
    }

    public function isRw(): bool
    {
        return $this->currentRole() === 'rw';
    }

    public function isBhabinkamtibmas(): bool
    {
        return $this->currentRole() === 'bhabinkamtibmas';
    }

    public function isNakes(): bool
    {
        return $this->currentRole() === 'nakes_puskesmas';
    }

    public function isPengurus(): bool
    {
        return in_array($this->currentRole(), ['rt', 'rw', 'bhabinkamtibmas']);
    }

    public function getRoleBadgeAttribute(): string
    {
        return match($this->currentRole()) {
            'warga' => 'Warga Lingkungan',
            'petugas_ronda' => 'Petugas Ronda',
            'rt' => 'Ketua RT ' . ($this->rt_id ?? '01'),
            'rw' => 'Pengurus RW ' . ($this->rw_id ?? '02'),
            'bhabinkamtibmas' => 'Bhabinkamtibmas',
            'nakes_puskesmas' => 'Nakes Puskesmas',
            default => 'Pengguna'
        };
    }
}
