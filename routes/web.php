<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardRwController;
use App\Http\Controllers\DeviceResetController;
use App\Http\Controllers\JagaWargaController;
use App\Http\Controllers\PanicAlertController;
use App\Http\Controllers\RondaController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
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
Route::get('/api/panic/data', [PanicAlertController::class, 'listJson'])->name('api.panic.list');
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
| 2. SISTEM AUTENTIKASI (LOGIN, LOGOUT, SELECT ROLE, & GANTI PERANGKAT)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/login/demo/{role}', [AuthController::class, 'quickDemoLogin'])->name('login.demo');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Alur Pengajuan Ganti Perangkat Warga (Bisa diakses dari form login)
Route::get('/device-reset', [AuthController::class, 'showDeviceResetForm'])->name('device.reset');
Route::post('/device-reset', [AuthController::class, 'submitDeviceReset'])->name('device.reset.post');

// Pemilihan Peran (Wajib Setelah Login) & Switch Role
Route::middleware('auth')->group(function () {
    Route::get('/select-role', [AuthController::class, 'showSelectRoleForm'])->name('role.select');
    Route::post('/select-role', [AuthController::class, 'selectRole'])->name('role.select.post');
    Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('role.switch');
});

/*
|--------------------------------------------------------------------------
| 3. HALAMAN TERPROTEKSI: PETUGAS RONDA POSKAMLING
|--------------------------------------------------------------------------
| Hanya dapat diakses oleh Petugas Ronda, Ketua RT, Pengurus RW, Bhabin, dan Nakes.
*/
Route::middleware(['auth', 'role:petugas_ronda,rt,rw,bhabinkamtibmas,nakes_puskesmas'])->group(function () {
    Route::get('/ronda', [RondaController::class, 'index'])->name('ronda.index');
    Route::post('/api/presensi', [RondaController::class, 'scanQr'])->name('api.presensi');
});

/*
|--------------------------------------------------------------------------
| 4. HALAMAN TERPROTEKSI: COMMAND CENTER PENGURUS RW, BHABINKAMTIBMAS & NAKES
|--------------------------------------------------------------------------
| Hanya dapat diakses oleh Ketua RT, Pengurus RW (Admin), Bhabinkamtibmas, dan Nakes Puskesmas.
*/
Route::middleware(['auth', 'role:rt,rw,bhabinkamtibmas,nakes_puskesmas'])->group(function () {
    Route::get('/dashboard', [DashboardRwController::class, 'index'])->name('dashboard.index');
    Route::post('/api/jadwal', [DashboardRwController::class, 'storeJadwal'])->name('api.jadwal');
    Route::delete('/api/jadwal/{id}', [DashboardRwController::class, 'deleteJadwal'])->name('api.jadwal.delete');
    Route::post('/api/wa-reminder', [DashboardRwController::class, 'sendWhatsappReminder'])->name('api.wa_reminder');
    Route::post('/api/settings/geofence', [DashboardRwController::class, 'updateGeofenceSettings'])->name('api.settings.geofence');

    // Manajemen Titik Rawan Patroli (Checkpoints)
    Route::post('/api/checkpoints', [DashboardRwController::class, 'storeCheckpoint'])->name('api.checkpoints.store');
    Route::put('/api/checkpoints/{id}', [DashboardRwController::class, 'updateCheckpoint'])->name('api.checkpoints.update');
    Route::delete('/api/checkpoints/{id}', [DashboardRwController::class, 'deleteCheckpoint'])->name('api.checkpoints.delete');

    // Manajemen Pengguna (User Management CRUD & Reset Perangkat)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/reset-device', [DeviceResetController::class, 'resetUserDevice'])->name('users.reset_device');

    // Manajemen Permohonan Ganti Perangkat Warga
    Route::get('/device-requests', [DeviceResetController::class, 'index'])->name('device.requests.index');
    Route::post('/device-requests/{id}/approve', [DeviceResetController::class, 'approve'])->name('device.requests.approve');
    Route::post('/device-requests/{id}/reject', [DeviceResetController::class, 'reject'])->name('device.requests.reject');

    // Pengaturan Sistem & Pembaruan Web (GitHub PAT, Symlink Storage)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/github', [SettingController::class, 'saveGithub'])->name('settings.github.save');
    Route::post('/settings/github/check', [SettingController::class, 'checkUpdate'])->name('settings.github.check');
    Route::post('/settings/github/update', [SettingController::class, 'executeUpdate'])->name('settings.github.update');
    Route::post('/settings/storage-link', [SettingController::class, 'generateStorageLink'])->name('settings.storage_link');

    // Manajemen Riwayat Kentongan (Hanya Admin / Pengurus: Edit & Hapus)
    Route::put('/api/panic/{id}', [PanicAlertController::class, 'update'])->name('api.panic.update');
    Route::delete('/api/panic/{id}', [PanicAlertController::class, 'destroy'])->name('api.panic.destroy');
});
