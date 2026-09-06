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

        // Jika tidak ada batasan role spesifik, cukup login
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah role pengguna ada dalam daftar role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Pengalihan cerdas jika role tidak sesuai
        if ($user->isWarga()) {
            return redirect()->route('warga.index')->with('warning', 'Akses dibatasi. Anda diarahkan ke portal warga.');
        }

        if ($user->isPetugasRonda()) {
            return redirect()->route('ronda.index')->with('warning', 'Akses dibatasi. Anda diarahkan ke portal petugas ronda.');
        }

        return redirect()->route('home')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
    }
}
