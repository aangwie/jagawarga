<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class PwaDevice extends Model
{
    use HasFactory;

    protected $table = 'pwa_devices';

    protected $fillable = [
        'device_id',
        'user_id',
        'is_pwa_installed',
        'notification_granted',
        'browser_info',
        'push_subscription',
        'last_active_at',
    ];

    protected $casts = [
        'is_pwa_installed' => 'boolean',
        'notification_granted' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk perangkat yang terpasang sebagai PWA
     */
    public function scopeInstalled($query)
    {
        return $query->where('is_pwa_installed', true);
    }

    /**
     * Scope untuk perangkat yang siap menerima notifikasi darurat
     */
    public function scopeNotificationReady($query)
    {
        return $query->where('notification_granted', true);
    }

    /**
     * Pastikan tabel pwa_devices tersedia di database
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('pwa_devices')) {
                Schema::create('pwa_devices', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('device_id')->unique()->index();
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->boolean('is_pwa_installed')->default(false);
                    $table->boolean('notification_granted')->default(false);
                    $table->string('browser_info')->nullable();
                    $table->text('push_subscription')->nullable();
                    $table->timestamp('last_active_at')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // Ignored if already created
        }
    }
}
