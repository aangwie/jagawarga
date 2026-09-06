<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $fillable = [
        'key',
        'value',
        'kategori',
        'keterangan',
    ];

    /**
     * Pastikan tabel pengaturan tersedia di database secara otomatis
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('pengaturan')) {
                Schema::create('pengaturan', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('key')->unique();
                    $table->longText('value')->nullable();
                    $table->string('kategori', 50)->default('system');
                    $table->string('keterangan')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel sudah terbuat oleh proses lain
        }
    }

    /**
     * Ambil nilai pengaturan berdasarkan key
     */
    public static function get(string $key, $default = null): ?string
    {
        static::ensureTableExists();

        try {
            $item = static::where('key', $key)->first();
            return $item ? $item->value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Simpan atau perbarui nilai pengaturan
     */
    public static function set(string $key, ?string $value, string $kategori = 'system', ?string $keterangan = null): self
    {
        static::ensureTableExists();

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'kategori' => $kategori,
                'keterangan' => $keterangan,
            ]
        );
    }

    /**
     * Ambil seluruh konfigurasi pembaruan GitHub
     */
    public static function getGithubConfig(): array
    {
        static::ensureTableExists();

        return [
            'github_pat' => static::get('github_pat', ''),
            'github_repo' => static::get('github_repo', 'aangwie/jagawarga'),
            'github_branch' => static::get('github_branch', 'main'),
            'last_update_at' => static::get('last_update_at', null),
            'last_update_status' => static::get('last_update_status', 'never'),
            'last_update_log' => static::get('last_update_log', ''),
        ];
    }
}
