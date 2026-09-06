<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BukuTamu extends Model
{
    use HasFactory;

    protected $table = 'buku_tamu';

    protected $fillable = [
        'nama_tamu',
        'kewarganegaraan',
        'nik',
        'nomor_paspor',
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

    /**
     * Pastikan tabel buku_tamu dan kolom kewarganegaraan & nomor_paspor tersedia
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('buku_tamu')) {
                Schema::create('buku_tamu', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('nama_tamu');
                    $table->enum('kewarganegaraan', ['WNI', 'WNA'])->default('WNI');
                    $table->string('nik', 16)->nullable();
                    $table->string('nomor_paspor', 50)->nullable();
                    $table->string('no_hp', 20);
                    $table->text('alamat_asal');
                    $table->text('tujuan_kunjungan');
                    $table->string('warga_yang_dikunjungi');
                    $table->string('rt', 5)->default('01');
                    $table->dateTime('tanggal_tiba');
                    $table->dateTime('tanggal_keluar')->nullable();
                    $table->enum('status', ['menunggu_verifikasi', 'disetujui', 'selesai'])->default('menunggu_verifikasi');
                    $table->string('foto_identitas')->nullable();
                    $table->text('keterangan')->nullable();
                    $table->timestamps();
                });
            } else {
                Schema::table('buku_tamu', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!Schema::hasColumn('buku_tamu', 'kewarganegaraan')) {
                        $table->enum('kewarganegaraan', ['WNI', 'WNA'])->default('WNI')->after('nama_tamu');
                    }
                    if (!Schema::hasColumn('buku_tamu', 'nomor_paspor')) {
                        $table->string('nomor_paspor', 50)->nullable()->after('nik');
                    }
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika kolom sudah ada
        }
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi RT',
            'disetujui' => 'Tamu Terverifikasi',
            'selesai' => 'Kunjungan Selesai',
            default => 'Tercatat',
        };
    }

    /**
     * Label identitas tamu (WNI dengan NIK atau WNA dengan Nomor Paspor)
     */
    public function getIdentitasFormattedAttribute(): string
    {
        if ($this->kewarganegaraan === 'WNA') {
            return '🌐 WNA • Paspor: ' . ($this->nomor_paspor ?: '-');
        }

        return '🇮🇩 WNI • NIK: ' . ($this->nik ?: '-');
    }
}
