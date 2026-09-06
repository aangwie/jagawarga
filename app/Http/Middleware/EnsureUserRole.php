<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Halaman ini terproteksi. Silakan masuk terlebih dahulu.');
        }

        $user = Auth::user();

        // 1. Pastikan pengguna telah memilih peran aktif untuk sesi ini
        if (!session()->has('active_role')) {
            return redirect()->route('role.select')
                ->with('warning', 'Silakan pilih peran yang ingin Anda gunakan untuk sesi ini sebelum melanjutkan.');
        }

        $activeRole = session('active_role');

        // 2. Jika peran aktif adalah 'warga', periksa konsistensi perangkat
        if ($activeRole === 'warga' && $user->isDeviceRegistered()) {
            $deviceId = $request->cookie('jagawarga_device_id');
            if ($deviceId && $user->registered_device_id !== $deviceId) {
                session()->forget('active_role');
                return redirect()->route('role.select')
                    ->with('error', 'Sesi dibatalkan: Perangkat ini tidak cocok dengan perangkat terdaftar akun Warga Anda.');
            }
        }

        // 3. Jika tidak ada batasan peran khusus pada rute, izinkan lewat
        if (empty($roles)) {
            return $next($request);
        }

        // 4. Periksa apakah peran aktif ada dalam daftar peran yang diizinkan untuk rute ini
        if (in_array($activeRole, $roles)) {
            return $next($request);
        }

        // 5. Pengalihan cerdas jika peran aktif tidak memiliki hak akses rute tersebut
        if ($user->isWarga()) {
            return redirect()->route('warga.index')->with('warning', 'Akses dibatasi. Anda diarahkan ke portal Warga.');
        }

        if ($user->isPetugasRonda()) {
            return redirect()->route('ronda.index')->with('warning', 'Akses dibatasi. Anda diarahkan ke portal Petugas Ronda.');
        }

        if ($user->isPengurus() || $user->isNakes()) {
            return redirect()->route('dashboard.index')->with('warning', 'Akses dibatasi. Anda diarahkan ke Command Center Pengurus & Nakes.');
        }

        return redirect()->route('home')->with('error', 'Peran aktif Anda (' . $user->role_badge . ') tidak memiliki izin mengakses halaman tersebut.');
    }
}
