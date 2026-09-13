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
        'tipe',
        'url_stream',
        'video_path',
        'status',
    ];

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Cek apakah sumber CCTV adalah YouTube
     */
    public function isYoutube(): bool
    {
        if ($this->tipe === 'upload') {
            return false;
        }
        $url = $this->url_stream ?? '';
        return (bool) preg_match('/(youtube\.com|youtu\.be)/i', $url);
    }

    /**
     * Konversi URL YouTube biasa ke embed URL autoplay muted loop
     */
    public function getYoutubeEmbedUrl(): string
    {
        $url = $this->url_stream ?? '';
        $videoId = '';

        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $matches)) {
            $videoId = $matches[1];
        }

        if (empty($videoId)) {
            return $url;
        }

        return "https://www.youtube-nocookie.com/embed/{$videoId}?autoplay=1&mute=1&loop=1&playlist={$videoId}&controls=0&modestbranding=1&rel=0&playsinline=1&enablejsapi=1";
    }

    /**
     * Cek apakah video adalah file yang diunggah
     */
    public function isUploadedVideo(): bool
    {
        return $this->tipe === 'upload' && !empty($this->video_path);
    }

    /**
     * Cek apakah sumber video berupa file video langsung (MP4, WebM, dll)
     */
    public function isDirectVideo(): bool
    {
        if ($this->isUploadedVideo()) {
            return true;
        }
        $url = strtolower($this->url_stream ?? '');
        return str_contains($url, '.mp4') || str_contains($url, '.webm') || str_contains($url, '.ogg') || str_contains($url, '.mov');
    }

    /**
     * Dapatkan URL sumber video (baik file lokal upload atau url stream)
     */
    public function getVideoSrc(): string
    {
        if ($this->isUploadedVideo()) {
            return asset($this->video_path);
        }
        return $this->url_stream ?? '';
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
                'tipe' => 'link',
                'url_stream' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'video_path' => null,
                'status' => 'aktif',
            ],
            [
                'id' => 2,
                'rt' => '01',
                'nama_lokasi' => 'CCTV 02 - Simpang Pos Ronda RW 02',
                'tipe' => 'link',
                'url_stream' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'video_path' => null,
                'status' => 'aktif',
            ],
            [
                'id' => 3,
                'rt' => '02',
                'nama_lokasi' => 'CCTV 03 - Taman Terbuka RT 02',
                'tipe' => 'link',
                'url_stream' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyBlazes.mp4',
                'video_path' => null,
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
                    $table->enum('tipe', ['link', 'upload'])->default('link');
                    $table->text('url_stream')->nullable();
                    $table->string('video_path')->nullable();
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
                if (!Schema::hasColumn('cctv_lingkungan', 'tipe')) {
                    Schema::table('cctv_lingkungan', function (Blueprint $table) {
                        $table->enum('tipe', ['link', 'upload'])->default('link')->after('nama_lokasi');
                    });
                }
                if (!Schema::hasColumn('cctv_lingkungan', 'url_stream')) {
                    Schema::table('cctv_lingkungan', function (Blueprint $table) {
                        $table->text('url_stream')->nullable()->after('tipe');
                    });
                }
                if (!Schema::hasColumn('cctv_lingkungan', 'video_path')) {
                    Schema::table('cctv_lingkungan', function (Blueprint $table) {
                        $table->string('video_path')->nullable()->after('url_stream');
                    });
                }
                if (!Schema::hasColumn('cctv_lingkungan', 'status')) {
                    Schema::table('cctv_lingkungan', function (Blueprint $table) {
                        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('video_path');
                    });
                }
            }

            try {
                if (self::count() === 0) {
                    foreach (self::getDefaultData() as $item) {
                        self::create([
                            'rt' => $item['rt'],
                            'nama_lokasi' => $item['nama_lokasi'],
                            'tipe' => $item['tipe'] ?? 'link',
                            'url_stream' => $item['url_stream'],
                            'video_path' => $item['video_path'] ?? null,
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
