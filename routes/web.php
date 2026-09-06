<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardRwController;
use App\Http\Controllers\JagaWargaController;
use App\Http\Controllers\RondaController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. HALAMAN & API PUBLIK (BEBAS AKSES TANPA LOGIN)
|--------------------------------------------------------------------------
| Warga dan pengunjung dapat mengakses beranda, modul panic button,
| pelaporan kejadian, dan pengisian buku tamu 2x24 jam tanpa hambatan.
*/
Route::get('/', [JagaWargaController::class, 'index'])->name('home');
Route::get('/warga', [WargaController::class, 'index'])->name('warga.index');

// API Publik
Route::post('/api/panic', [WargaController::class, 'storePanic'])->name('api.panic');
Route::post('/api/lapor', [WargaController::class, 'storeLaporan'])->name('api.lapor');
Route::post('/api/buku-tamu', [WargaController::class, 'storeBukuTamu'])->name('api.buku_tamu');
Route::get('/api/settings/public', function () {
    $setting = \App\Models\RwSetting::getActiveSetting();
    return response()->json([
        'success' => true,
        'nama_rw' => $setting->nama_rw,
        'center_latitude' => (float)$setting->center_latitude,
        'center_longitude' => (float)$setting->center_longitude,
        'panic_radius_meters' => (int)$setting->panic_radius_meters,
        'kontak_darurat' => $setting->kontak_darurat,
    ]);
})->name('api.settings.public');

/*
|--------------------------------------------------------------------------
| 2. SISTEM AUTENTIKASI (LOGIN & LOGOUT)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/login/demo/{role}', [AuthController::class, 'quickDemoLogin'])->name('login.demo');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 3. HALAMAN TERPROTEKSI: PETUGAS RONDA POSKAMLING
|--------------------------------------------------------------------------
| Hanya dapat diakses oleh Petugas Ronda, Ketua RT, Pengurus RW, dan Bhabin.
*/
Route::middleware(['auth', 'role:petugas_ronda,rt,rw,bhabinkamtibmas'])->group(function () {
    Route::get('/ronda', [RondaController::class, 'index'])->name('ronda.index');
    Route::post('/api/presensi', [RondaController::class, 'scanQr'])->name('api.presensi');
});

/*
|--------------------------------------------------------------------------
| 4. HALAMAN TERPROTEKSI: COMMAND CENTER PENGURUS RW & BHABINKAMTIBMAS
|--------------------------------------------------------------------------
| Hanya dapat diakses oleh Ketua RT, Pengurus RW (Admin), dan Bhabinkamtibmas.
*/
Route::middleware(['auth', 'role:rt,rw,bhabinkamtibmas'])->group(function () {
    Route::get('/dashboard', [DashboardRwController::class, 'index'])->name('dashboard.index');
    Route::post('/api/jadwal', [DashboardRwController::class, 'storeJadwal'])->name('api.jadwal');
    Route::delete('/api/jadwal/{id}', [DashboardRwController::class, 'deleteJadwal'])->name('api.jadwal.delete');
    Route::post('/api/wa-reminder', [DashboardRwController::class, 'sendWhatsappReminder'])->name('api.wa_reminder');
    Route::post('/api/settings/geofence', [DashboardRwController::class, 'updateGeofenceSettings'])->name('api.settings.geofence');
});
