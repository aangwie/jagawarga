<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CctvLingkungan extends Model
{
    use HasFactory;

    protected $table = 'cctv_lingkungan';

    protected $fillable = [
        'rt',
        'nama_lokasi',
        'url_stream',
        'status',
    ];

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Data default kamera CCTV lingkungan RW 02
     */
    public static function getDefaultData(): array
    {
        return [
            [
                'id' => 1,
                'rt' => '01',
                'nama_lokasi' => 'CCTV 01 - Gapura Masuk Utama RW 02',
                'url_stream' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'status' => 'aktif',
            ],
            [
                'id' => 2,
                'rt' => '01',
                'nama_lokasi' => 'CCTV 02 - Simpang Pos Ronda RW 02',
                'url_stream' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'status' => 'aktif',
            ],
            [
                'id' => 3,
                'rt' => '02',
                'nama_lokasi' => 'CCTV 03 - Taman Terbuka RT 02',
                'url_stream' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'status' => 'aktif',
            ],
        ];
    }

    /**
     * Pastikan tabel cctv_lingkungan ada dan terisi data default (self-healing untuk hosting)
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('cctv_lingkungan')) {
                Schema::create('cctv_lingkungan', function (Blueprint $table) {
                    $table->id();
                    $table->string('rt', 5)->default('01');
                    $table->string('nama_lokasi');
                    $table->text('url_stream')->nullable();
                    $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                    $table->timestamps();
                });
            } else {
                if (!Schema::hasColumn('cctv_lingkungan', 'rt')) {
                    Schema::table('cctv_lingkungan', function (Blueprint $table) {
                        $table->string('rt', 5)->default('01')->after('id');
                    });
                }
                if (!Schema::hasColumn('cctv_lingkungan', 'nama_lokasi')) {
                    Schema::table('cctv_lingkungan', function (Blueprint $table) {
                        $table->string('nama_lokasi')->after('rt');
                    });
                }
                if (!Schema::hasColumn('cctv_lingkungan', 'url_stream')) {
                    Schema::table('cctv_lingkungan', function (Blueprint $table) {
                        $table->text('url_stream')->nullable()->after('nama_lokasi');
                    });
                }
                if (!Schema::hasColumn('cctv_lingkungan', 'status')) {
                    Schema::table('cctv_lingkungan', function (Blueprint $table) {
                        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('url_stream');
                    });
                }
            }

            try {
                if (self::count() === 0) {
                    foreach (self::getDefaultData() as $item) {
                        self::create([
                            'rt' => $item['rt'],
                            'nama_lokasi' => $item['nama_lokasi'],
                            'url_stream' => $item['url_stream'],
                            'status' => $item['status'],
                        ]);
                    }
                }
            } catch (\Throwable $seedEx) {
                Log::warning('CCTV seed notice: ' . $seedEx->getMessage());
            }
        } catch (\Throwable $e) {
            Log::error('CctvLingkungan ensureTableExists error: ' . $e->getMessage());
        }
    }
}
