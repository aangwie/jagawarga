<?php

namespace App\Http\Controllers;

use App\Models\DeviceResetRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (session()->has('active_role')) {
                return $this->redirectBasedOnRole(Auth::user());
            }
            return redirect()->route('role.select');
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

            // Pastikan user memiliki relasi role di role_user jika belum tersinkron
            $this->ensureUserRolesSynchronized($user);

            // Bersihkan active_role sebelumnya agar user wajib memilih peran
            $request->session()->forget('active_role');

            // Tangkap device_id dari cookie atau input dan perbarui cookie
            $deviceId = $request->input('device_id') ?? $request->cookie('jagawarga_device_id') ?? ('dev_' . bin2hex(random_bytes(16)));
            Cookie::queue('jagawarga_device_id', $deviceId, 60 * 24 * 365); // 1 tahun

            return redirect()->route('role.select')
                ->with('info', 'Autentikasi berhasil! Silakan tentukan peran yang ingin Anda gunakan untuk sesi ini.');
        }

        return back()->withErrors([
            'login' => 'Email / NIK atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('login');
    }

    /**
     * Layar Pemilihan Peran (Select Role)
     */
    public function showSelectRoleForm(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan masuk terlebih dahulu.');
        }

        $user = Auth::user();
        $this->ensureUserRolesSynchronized($user);

        // Ambil daftar peran yang dimiliki oleh pengguna
        $roles = $user->roles()->orderBy('id')->get();

        // Jika belum ada peran sama sekali, buatkan peran default 'warga'
        if ($roles->isEmpty()) {
            $wargaRole = Role::where('name', 'warga')->first();
            if ($wargaRole) {
                $user->roles()->attach($wargaRole->id);
                $roles = collect([$wargaRole]);
            }
        }

        // Ambil device_id dari cookie atau buatkan default
        $currentDeviceId = $request->cookie('jagawarga_device_id') ?? ('dev_' . bin2hex(random_bytes(16)));
        Cookie::queue('jagawarga_device_id', $currentDeviceId, 60 * 24 * 365);

        return view('auth.select-role', compact('user', 'roles', 'currentDeviceId'));
    }

    /**
     * Proses Pemilihan Peran Aktif & Validasi Perangkat Warga
     */
    public function selectRole(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Sesi Anda telah kedaluwarsa. Silakan masuk kembali.');
        }

        $request->validate([
            'role' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        $user = Auth::user();
        $selectedRole = $request->role;

        // Validasi apakah pengguna memang berhak atas peran yang dipilih
        if (!$user->hasRole($selectedRole)) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk peran "' . $selectedRole . '".');
        }

        // Identifier Perangkat
        $deviceId = $request->input('device_id') ?: $request->cookie('jagawarga_device_id');
        if (!$deviceId) {
            $deviceId = 'dev_' . bin2hex(random_bytes(16));
        }
        Cookie::queue('jagawarga_device_id', $deviceId, 60 * 24 * 365);

        // ATURAN KEAMANAN: 1 Akun Warga Hanya untuk 1 Perangkat Terdaftar
        if ($selectedRole === 'warga') {
            if (!$user->isDeviceRegistered()) {
                // Perangkat pertama: daftarkan perangkat ini secara otomatis
                $user->update([
                    'registered_device_id' => $deviceId,
                    'device_info' => substr($request->userAgent() ?? 'Peramban Web', 0, 250),
                    'device_registered_at' => now(),
                ]);
            } else {
                // Sudah ada perangkat terdaftar: bandingkan dengan perangkat saat ini
                if ($user->registered_device_id !== $deviceId) {
                    return back()->with('device_error', [
                        'title' => 'Akses Peran Warga Terkunci',
                        'message' => 'Akun Warga Anda sudah terdaftar di perangkat lain. Kebijakan keamanan membatasi 1 akun warga hanya boleh digunakan pada 1 perangkat terdaftar.',
                        'registered_device' => $user->device_info ?? 'Perangkat Terdaftar',
                        'registered_at' => $user->device_registered_at ? $user->device_registered_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Waktu tidak tercatat',
                    ]);
                }
            }
        }

        // Simpan peran aktif ke dalam sesi
        session(['active_role' => $selectedRole]);

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Selamat bertugas, ' . $user->name . ' sebagai ' . $user->role_badge . '!');
    }

    /**
     * Switch Role langsung dari header navbar
     */
    public function switchRole(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'role' => 'required|string',
        ]);

        $user = Auth::user();
        $targetRole = $request->role;

        if (!$user->hasRole($targetRole)) {
            return back()->with('error', 'Anda tidak memiliki hak untuk peran tersebut.');
        }

        // Pengecekan perangkat jika berganti ke peran warga
        if ($targetRole === 'warga') {
            $deviceId = $request->cookie('jagawarga_device_id');
            if ($user->isDeviceRegistered() && $user->registered_device_id !== $deviceId) {
                return back()->with('error', 'Tidak dapat beralih ke peran Warga karena akun Anda terikat di perangkat lain.');
            }
        }

        session(['active_role' => $targetRole]);

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Peran berhasil dialihkan ke ' . $user->role_badge . '.');
    }

    /**
     * Tampilkan formulir pengajuan ganti perangkat
     */
    public function showDeviceResetForm()
    {
        return view('auth.device-reset');
    }

    /**
     * Proses pengajuan permohonan ganti perangkat oleh warga
     */
    public function submitDeviceReset(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16',
            'nama_ibu' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'alasan' => 'nullable|string|max:500',
            'device_id' => 'nullable|string',
        ]);

        // Cek apakah user dengan NIK tersebut ada
        $user = User::where('nik', $validated['nik'])->first();

        if (!$user) {
            return back()->withErrors([
                'nik' => 'NIK tidak terdaftar dalam basis data sistem JagaWarga RW 02.',
            ])->withInput();
        }

        // Cek permohonan yang masih pending
        $pendingRequest = DeviceResetRequest::where('nik', $validated['nik'])
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return back()->with('warning', 'Anda sudah memiliki permohonan ganti perangkat yang sedang menunggu persetujuan Pengurus RT/RW (Diajukan pada ' . $pendingRequest->created_at->translatedFormat('d M Y H:i') . ' WIB).')->withInput();
        }

        $deviceId = $validated['device_id'] ?? $request->cookie('jagawarga_device_id') ?? ('dev_' . bin2hex(random_bytes(16)));
        Cookie::queue('jagawarga_device_id', $deviceId, 60 * 24 * 365);

        // Buat rekaman permohonan ganti perangkat
        DeviceResetRequest::create([
            'user_id' => $user->id,
            'nik' => $validated['nik'],
            'nama_ibu' => $validated['nama_ibu'],
            'phone' => $validated['phone'],
            'alasan' => $validated['alasan'] ?? 'Ganti perangkat baru / perangkat lama rusak',
            'new_device_id' => $deviceId,
            'new_device_info' => substr($request->userAgent() ?? 'Perangkat Baru', 0, 250),
            'status' => 'pending',
        ]);

        return redirect()->route('login')
            ->with('success', 'Permohonan ganti perangkat untuk NIK ' . $validated['nik'] . ' berhasil dikirimkan. Pengurus RW/RT akan memverifikasi data Anda segera.');
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
            'nakes_puskesmas' => 'nakes@jagawarga.local',
            'nakes' => 'nakes@jagawarga.local',
            'warga' => 'warga@jagawarga.local',
        ];

        $targetEmail = $emailMap[$role] ?? 'warga@jagawarga.local';
        $user = User::where('email', $targetEmail)->first();

        if (!$user) {
            $nikMap = [
                'rw' => '3201010101010097',
                'rt' => '3201010101010095',
                'petugas_ronda' => '3201010101010093',
                'ronda' => '3201010101010093',
                'bhabinkamtibmas' => '3201010101010098',
                'bhabin' => '3201010101010098',
                'nakes_puskesmas' => '3201010101010099',
                'nakes' => '3201010101010099',
                'warga' => '3201010101010091',
            ];

            $user = User::firstOrCreate(
                ['email' => $targetEmail],
                [
                    'name' => match($role) {
                        'rw' => 'Pak Gunawan (Ketua RW 02)',
                        'rt' => 'Pak Bambang (Ketua RT 01)',
                        'petugas_ronda', 'ronda' => 'Pak Joko Ronda',
                        'bhabinkamtibmas', 'bhabin' => 'Aiptu Hendro Prasetyo',
                        'nakes', 'nakes_puskesmas' => 'dr. Sarah Amalia (Nakes Puskesmas)',
                        default => 'Budi Santoso'
                    },
                    'nik' => $nikMap[$role] ?? ('320101010101' . str_pad(random_int(10, 99), 4, '0', STR_PAD_LEFT)),
                    'phone' => '081234567890',
                    'nama_ibu' => 'Siti Aminah',
                    'password' => bcrypt('password'),
                    'role' => in_array($role, ['rw', 'rt', 'petugas_ronda', 'bhabinkamtibmas', 'nakes_puskesmas', 'nakes', 'warga']) ? (in_array($role, ['nakes', 'nakes_puskesmas']) ? 'nakes_puskesmas' : $role) : 'petugas_ronda',
                    'rt_id' => '01',
                    'rw_id' => '02',
                ]
            );
        }

        Auth::login($user);
        request()->session()->regenerate();

        $this->ensureUserRolesSynchronized($user);

        // Tetapkan active_role sesuai role yang dipilih di demo
        $targetRoleName = match($role) {
            'ronda' => 'petugas_ronda',
            'bhabin' => 'bhabinkamtibmas',
            'nakes' => 'nakes_puskesmas',
            default => $role
        };

        // Pastikan role ini terikat pada user
        $roleModel = Role::where('name', $targetRoleName)->first();
        if ($roleModel && !$user->roles->contains($roleModel->id)) {
            $user->roles()->attach($roleModel->id);
        }

        // Bounding device untuk warga pada mode demo
        if ($targetRoleName === 'warga' && !$user->isDeviceRegistered()) {
            $deviceId = request()->cookie('jagawarga_device_id') ?? ('dev_' . bin2hex(random_bytes(16)));
            Cookie::queue('jagawarga_device_id', $deviceId, 60 * 24 * 365);
            $user->update([
                'registered_device_id' => $deviceId,
                'device_info' => 'Peramban Demo Testing',
                'device_registered_at' => now(),
            ]);
        }

        session(['active_role' => $targetRoleName]);

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
     * Helper pengalihan halaman berdasarkan peran pengguna yang aktif
     */
    private function redirectBasedOnRole($user)
    {
        if ($user->isPengurus() || $user->isNakes()) {
            return redirect()->intended(route('dashboard.index'));
        }

        if ($user->isPetugasRonda()) {
            return redirect()->intended(route('ronda.index'));
        }

        return redirect()->intended(route('warga.index'));
    }

    /**
     * Pastikan role dari tabel users tersinkron ke role_user
     */
    private function ensureUserRolesSynchronized(User $user)
    {
        if ($user->roles()->count() === 0) {
            $roleSlug = $user->getRawOriginal('role') ?: 'warga';
            $roleObj = Role::where('name', $roleSlug)->first();
            if ($roleObj) {
                $user->roles()->attach($roleObj->id);
            }
        }
    }
}
