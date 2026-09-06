<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

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
        'tingkat_kerawanan',
        'urutan_patroli',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'urutan_patroli' => 'integer',
    ];

    /**
     * Pastikan tabel checkpoints dan kolom pendukung tersedia
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('checkpoints')) {
                Schema::create('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('rt', 5)->default('01');
                    $table->string('nama_titik');
                    $table->string('kode_qr')->unique();
                    $table->decimal('latitude', 10, 8)->nullable();
                    $table->decimal('longitude', 11, 8)->nullable();
                    $table->text('deskripsi')->nullable();
                    $table->enum('tingkat_kerawanan', ['rawan', 'sedang', 'aman'])->default('rawan');
                    $table->integer('urutan_patroli')->default(1);
                    $table->timestamps();
                });
            } else {
                if (!Schema::hasColumn('checkpoints', 'rt')) {
                    Schema::table('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('rt', 5)->default('01')->after('id');
                    });
                }
                if (!Schema::hasColumn('checkpoints', 'nama_titik')) {
                    Schema::table('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('nama_titik')->default('Titik Patroli')->after('rt');
                    });
                }
                if (!Schema::hasColumn('checkpoints', 'kode_qr')) {
                    Schema::table('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('kode_qr')->nullable()->after('nama_titik');
                    });
                }
                if (!Schema::hasColumn('checkpoints', 'latitude')) {
                    Schema::table('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->decimal('latitude', 10, 8)->nullable()->after('kode_qr');
                    });
                }
                if (!Schema::hasColumn('checkpoints', 'longitude')) {
                    Schema::table('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
                    });
                }
                if (!Schema::hasColumn('checkpoints', 'deskripsi')) {
                    Schema::table('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->text('deskripsi')->nullable()->after('longitude');
                    });
                }
                if (!Schema::hasColumn('checkpoints', 'tingkat_kerawanan')) {
                    Schema::table('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->enum('tingkat_kerawanan', ['rawan', 'sedang', 'aman'])->default('rawan')->after('deskripsi');
                    });
                }
                if (!Schema::hasColumn('checkpoints', 'urutan_patroli')) {
                    Schema::table('checkpoints', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->integer('urutan_patroli')->default(1)->after('tingkat_kerawanan');
                    });
                }
            }

            try {
                if (self::count() === 0) {
                    self::create([
                        'rt' => '01',
                        'nama_titik' => 'Pos Utama RW 02',
                        'kode_qr' => 'JW-CKP-001-POSRW',
                        'latitude' => -6.208800,
                        'longitude' => 106.845600,
                        'deskripsi' => 'Pusat koordinasi & kentongan digital siaga 24 jam.',
                        'tingkat_kerawanan' => 'aman',
                        'urutan_patroli' => 1,
                    ]);
                    self::create([
                        'rt' => '01',
                        'nama_titik' => 'Gapura Masuk Blok A',
                        'kode_qr' => 'JW-CKP-002-GAPURA',
                        'latitude' => -6.207850,
                        'longitude' => 106.846200,
                        'deskripsi' => 'Portal perbatasan lingkungan, wajib digembok pukul 23:00 WIB.',
                        'tingkat_kerawanan' => 'sedang',
                        'urutan_patroli' => 2,
                    ]);
                    self::create([
                        'rt' => '02',
                        'nama_titik' => 'Taman RT 02 (Minim PJU)',
                        'kode_qr' => 'JW-CKP-003-TAMAN',
                        'latitude' => -6.209600,
                        'longitude' => 106.844700,
                        'deskripsi' => 'Area taman terbuka dengan penerangan terbatas, rawan tindak asusila/kejahatan malam.',
                        'tingkat_kerawanan' => 'sedang',
                        'urutan_patroli' => 3,
                    ]);
                    self::create([
                        'rt' => '02',
                        'nama_titik' => 'Gardu Gang Senggol (Titik Rawan)',
                        'kode_qr' => 'JW-CKP-004-GARDU',
                        'latitude' => -6.210200,
                        'longitude' => 106.847100,
                        'deskripsi' => 'Jalan buntu dan sepi, rawan pencurian kendaraan bermotor (curanmor). Patroli wajib memantau tiap 1 jam.',
                        'tingkat_kerawanan' => 'rawan',
                        'urutan_patroli' => 4,
                    ]);
                }
            } catch (\Throwable $seedEx) {
                \Illuminate\Support\Facades\Log::warning('Checkpoint seed notice: ' . $seedEx->getMessage());
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Checkpoint ensureTableExists error: ' . $e->getMessage());
        }
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(PresensiRonda::class, 'checkpoint_id');
    }

    /**
     * URL Google Maps titik rawan / checkpoint
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        if (!$this->latitude || !$this->longitude) {
            return '#';
        }
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    /**
     * Label Status Tingkat Kerawanan
     */
    public function getTingkatKerawananBadgeAttribute(): string
    {
        return match ($this->tingkat_kerawanan) {
            'aman' => '🟢 Aman / Pos Pantau',
            'sedang' => '🟡 Kerawanan Sedang',
            default => '🔴 Titik Rawan Prioritas',
        };
    }

    /**
     * Kelas CSS Badge
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->tingkat_kerawanan) {
            'aman' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'sedang' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
