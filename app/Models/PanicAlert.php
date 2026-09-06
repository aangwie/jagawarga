<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class PanicAlert extends Model
{
    use HasFactory;

    protected $table = 'panic_alerts';

    protected $fillable = [
        'user_id',
        'pelapor_nama',
        'latitude',
        'longitude',
        'kategori',
        'status',
        'catatan',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Pastikan tabel panic_alerts ada dan memiliki semua kolom yang diperlukan
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('panic_alerts')) {
                Schema::create('panic_alerts', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->string('pelapor_nama')->nullable();
                    $table->decimal('latitude', 10, 8);
                    $table->decimal('longitude', 11, 8);
                    $table->string('kategori', 50)->default('pencurian');
                    $table->string('status', 50)->default('aktif');
                    $table->text('catatan')->nullable();
                    $table->timestamps();
                });
            } else {
                // Pastikan kolom pelapor_nama tersedia
                if (!Schema::hasColumn('panic_alerts', 'pelapor_nama')) {
                    Schema::table('panic_alerts', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('pelapor_nama')->nullable()->after('user_id');
                    });
                }

                try {
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE panic_alerts MODIFY user_id BIGINT UNSIGNED NULL');
                } catch (\Throwable $e) {
                    // Abaikan jika alter gagal atau tidak didukung
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel atau kolom sudah ada
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nama pelapor (dari relasi user atau fallback nama pelapor)
     */
    public function getNamaPelaporAttribute(): string
    {
        if (!empty($this->pelapor_nama)) {
            return $this->pelapor_nama;
        }

        if ($this->user) {
            return $this->user->name;
        }

        return 'Warga Lingkungan (Tamu)';
    }

    /**
     * URL Google Maps langsung ke titik koordinat kejadian
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    /**
     * Format Waktu & Tanggal Kejadian (Lokal Indonesia)
     */
    public function getWaktuFormattedAttribute(): string
    {
        return $this->created_at ? $this->created_at->translatedFormat('d M Y, H:i:s') . ' WIB' : '-';
    }

    /**
     * Label Kategori Kejadian
     */
    public function getKategoriBadgeAttribute(): string
    {
        return match ($this->kategori) {
            'pencurian' => 'Pencurian / Maling',
            'kebakaran' => 'Bahaya Kebakaran',
            'medis' => 'Darurat Medis',
            default => 'Siaga Lingkungan / Lainnya',
        };
    }

    /**
     * Emoji Icon Kategori
     */
    public function getKategoriIconAttribute(): string
    {
        return match ($this->kategori) {
            'pencurian' => '🚨',
            'kebakaran' => '🔥',
            'medis' => '🚑',
            default => '⚠️',
        };
    }
}
