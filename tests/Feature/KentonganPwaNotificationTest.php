<?php

namespace Tests\Feature;

use App\Models\PanicAlert;
use App\Models\PwaDevice;
use App\Models\Role;
use App\Models\RwSetting;
use App\Models\User;
use Tests\TestCase;

class KentonganPwaNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'warga'], [
            'display_name' => 'Warga Lingkungan',
            'description' => 'Akses Panic Button, Laporan, Buku Tamu.',
            'icon' => '🏠',
            'badge_color' => 'bg-emerald-100 text-emerald-800'
        ]);

        RwSetting::ensureTableExists();
        PanicAlert::ensureTableExists();
        PwaDevice::ensureTableExists();

        // Bersihkan data test
        User::where('email', 'like', '%@testpwa.local')->delete();
        PwaDevice::where('device_id', 'like', 'dev_test_%')->delete();
    }

    protected function tearDown(): void
    {
        User::where('email', 'like', '%@testpwa.local')->delete();
        PwaDevice::where('device_id', 'like', 'dev_test_%')->delete();
        parent::tearDown();
    }

    /**
     * Uji registrasi perangkat PWA dan status izin notifikasi
     */
    public function test_pwa_device_can_be_registered(): void
    {
        $response = $this->postJson(route('api.pwa.register'), [
            'device_id' => 'dev_test_mobile_pwa_001',
            'is_pwa_installed' => true,
            'notification_granted' => true,
            'browser_info' => 'Mozilla/5.0 Android Chrome PWA Test',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'device' => [
                'device_id' => 'dev_test_mobile_pwa_001',
                'is_pwa_installed' => true,
                'notification_granted' => true,
            ]
        ]);

        $this->assertDatabaseHas('pwa_devices', [
            'device_id' => 'dev_test_mobile_pwa_001',
            'is_pwa_installed' => 1,
            'notification_granted' => 1,
        ]);
    }

    /**
     * Uji endpoint statistik perangkat PWA
     */
    public function test_pwa_device_status_endpoint(): void
    {
        PwaDevice::create([
            'device_id' => 'dev_test_status_01',
            'is_pwa_installed' => true,
            'notification_granted' => true,
            'last_active_at' => now(),
        ]);

        $response = $this->getJson(route('api.pwa.status'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'total_devices',
            'installed_pwa',
            'notification_ready',
            'pwa_with_notifications',
        ]);
        $this->assertTrue($response->json('installed_pwa') >= 1);
    }

    /**
     * Uji warga yang belum diverifikasi NIK ditolak membunyikan kentongan online
     */
    public function test_unverified_citizen_cannot_trigger_kentongan(): void
    {
        $unverified = User::create([
            'name' => 'Warga Belum Terverifikasi',
            'nik' => '3201999900010001',
            'email' => 'unverified@testpwa.local',
            'password' => bcrypt('password'),
            'role' => 'warga',
            'is_nik_verified' => false,
        ]);

        $this->actingAs($unverified);

        $response = $this->postJson(route('api.panic'), [
            'latitude' => -6.2297,
            'longitude' => 106.8295,
            'kategori' => 'pencurian',
            'catatan' => 'Uji coba penolakan belum verifikasi',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'unverified' => true,
        ]);
    }

    /**
     * Uji warga terverifikasi dalam radius geofence sukses membunyikan kentongan dan menghasilkan broadcast PWA
     */
    public function test_verified_citizen_inside_geofence_triggers_panic_and_pwa_broadcast(): void
    {
        // Daftarkan dummy PWA device agar target count terverifikasi
        PwaDevice::create([
            'device_id' => 'dev_test_target_broadcast',
            'is_pwa_installed' => true,
            'notification_granted' => true,
            'last_active_at' => now(),
        ]);

        $verified = User::create([
            'name' => 'Budi Terverifikasi',
            'nik' => '3201888800020002',
            'email' => 'budi_verified@testpwa.local',
            'password' => bcrypt('password'),
            'role' => 'warga',
            'is_nik_verified' => true,
        ]);

        $setting = RwSetting::getActiveSetting();

        $this->actingAs($verified);

        $response = $this->postJson(route('api.panic'), [
            'latitude' => (float)$setting->center_latitude,
            'longitude' => (float)$setting->center_longitude,
            'kategori' => 'kebakaran',
            'catatan' => 'Asap tebal di RT 02 Blok B',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'pwa_broadcast' => [
                'status' => 'dispatched',
            ]
        ]);
        $this->assertNotEmpty($response->json('alert.id'));
        $this->assertEquals('kebakaran', $response->json('alert.kategori'));

        // Cek bahwa alert berstatus aktif tersimpan di database
        $this->assertDatabaseHas('panic_alerts', [
            'id' => $response->json('alert.id'),
            'kategori' => 'kebakaran',
            'status' => 'aktif',
        ]);
    }

    /**
     * Uji endpoint /api/panic/latest-active mengembalikan data kentongan untuk sinkronisasi seluruh perangkat PWA
     */
    public function test_latest_active_endpoint_returns_active_alert_for_pwa_devices(): void
    {
        $verified = User::create([
            'name' => 'Siti Terverifikasi',
            'nik' => '3201777700030003',
            'email' => 'siti_verified@testpwa.local',
            'password' => bcrypt('password'),
            'role' => 'warga',
            'is_nik_verified' => true,
        ]);

        $setting = RwSetting::getActiveSetting();

        $this->actingAs($verified);

        $panicResponse = $this->postJson(route('api.panic'), [
            'latitude' => (float)$setting->center_latitude,
            'longitude' => (float)$setting->center_longitude,
            'kategori' => 'medis',
            'catatan' => 'Warga butuh ambulans darurat',
        ]);

        $panicResponse->assertStatus(200);
        $alertId = $panicResponse->json('alert.id');

        // PWA device lain meminta sinyal darurat aktif terkini
        $syncResponse = $this->getJson(route('api.panic.latest_active'));

        $syncResponse->assertStatus(200);
        $syncResponse->assertJson([
            'success' => true,
            'has_active' => true,
            'alert' => [
                'id' => $alertId,
                'kategori' => 'medis',
                'status' => 'aktif',
            ]
        ]);
    }

    /**
     * Uji ketersediaan Service Worker (sw.js) dan dukungannya terhadap event notifikasi
     */
    public function test_service_worker_file_contains_emergency_notification_handlers(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);

        $swContent = file_get_contents($swPath);
        $this->assertStringContainsString('SHOW_PANIC_NOTIFICATION', $swContent);
        $this->assertStringContainsString('notificationclick', $swContent);
        $this->assertStringContainsString('showNotification', $swContent);
        $this->assertStringContainsString('vibrate', $swContent);
    }
}
