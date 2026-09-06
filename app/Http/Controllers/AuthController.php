<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        // Dukungan login via Email atau NIK 16 digit
        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        if (Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            return $this->redirectBasedOnRole($user)
                ->with('success', 'Selamat datang kembali, ' . $user->name . ' (' . $user->role_badge . ')');
        }

        return back()->withErrors([
            'login' => 'Email / NIK atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('login');
    }

    /**
     * Demo Login 1-Klik untuk kemudahan pengujian semua peran
     */
    public function quickDemoLogin($role)
    {
        $emailMap = [
            'rw' => 'rw02@jagawarga.local',
            'rt' => 'rt01@jagawarga.local',
            'petugas_ronda' => 'ronda@jagawarga.local',
            'ronda' => 'ronda@jagawarga.local',
            'bhabinkamtibmas' => 'bhabin@jagawarga.local',
            'bhabin' => 'bhabin@jagawarga.local',
            'warga' => 'warga@jagawarga.local',
        ];

        $targetEmail = $emailMap[$role] ?? 'warga@jagawarga.local';
        $user = User::where('email', $targetEmail)->first();

        if (!$user) {
            // Jika user belum ada di DB, buatkan secara dinamis untuk kenyamanan uji coba
            $user = User::firstOrCreate(
                ['email' => $targetEmail],
                [
                    'name' => match($role) {
                        'rw' => 'Pak Gunawan (Ketua RW 02)',
                        'rt' => 'Pak Bambang (Ketua RT 01)',
                        'petugas_ronda', 'ronda' => 'Pak Joko Ronda',
                        'bhabinkamtibmas', 'bhabin' => 'Aiptu Hendro Prasetyo',
                        default => 'Budi Santoso'
                    },
                    'nik' => '3201010101010099',
                    'phone' => '081234567890',
                    'password' => bcrypt('password'),
                    'role' => in_array($role, ['rw', 'rt', 'petugas_ronda', 'bhabinkamtibmas', 'warga']) ? $role : 'petugas_ronda',
                    'rt_id' => '01',
                    'rw_id' => '02',
                ]
            );
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Berhasil masuk dalam mode simulasi: ' . $user->name . ' (' . $user->role_badge . ')');
    }

    /**
     * Logout pengguna
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Helper pengalihan halaman berdasarkan peran pengguna
     */
    private function redirectBasedOnRole($user)
    {
        if ($user->isPengurus()) {
            return redirect()->intended(route('dashboard.index'));
        }

        if ($user->isPetugasRonda()) {
            return redirect()->intended(route('ronda.index'));
        }

        return redirect()->intended(route('warga.index'));
    }
}
