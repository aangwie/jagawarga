@extends('layouts.app')

@section('title', 'Masuk Sistem - JagaWarga RW 02')

@section('content')
<div class="max-w-md mx-auto py-4 space-y-6">

    <!-- Tombol Kembali ke Halaman Publik -->
    <div>
        <a href="/" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Halaman Publik Bebas Akses</span>
        </a>
    </div>

    <!-- Kartu Login Utama -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-lg space-y-5">
        
        <div class="text-center space-y-1.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center mx-auto shadow-md shadow-emerald-600/30">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Masuk JagaWarga RW</h1>
            <p class="text-xs text-slate-500">Khusus Petugas Ronda Poskamling & Pengurus RT/RW</p>
        </div>

        @if(session('warning'))
        <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold text-center animate-in fade-in">
            ⚠️ {{ session('warning') }}
        </div>
        @endif

        @if(session('error'))
        <div class="p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in">
            🚫 {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold space-y-0.5">
            @foreach($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="device_id" id="hidden-login-device-id" value="">

            <div>
                <label for="login" class="block text-xs font-bold text-slate-700 mb-1">Email atau NIK (16 Digit)</label>
                <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus placeholder="contoh: ronda@jagawarga.local / NIK" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                        <span id="eye-text">Lihat</span>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span class="text-slate-600 font-medium">Ingat Saya</span>
                </label>
                <span class="text-[11px] text-slate-400">Default sandi: <strong>password</strong></span>
            </div>

            <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-700/20 transition cursor-pointer">
                Masuk ke Sistem
            </button>
        </form>

        <!-- Opsi Ajukan Ganti Perangkat Warga -->
        <div class="p-3 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">📱</span>
                <div>
                    <span class="text-amber-950 font-bold text-[11px] block">Ganti HP / Perangkat Warga?</span>
                    <span class="text-amber-700/90 text-[10px] block">1 akun warga dibatasi 1 perangkat terdaftar</span>
                </div>
            </div>
            <a href="{{ route('device.reset') }}" class="px-2.5 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-[10px] transition shrink-0">
                Ajukan Reset
            </a>
        </div>

        <div class="pt-2 border-t border-slate-100 text-center">
            <p class="text-[11px] text-slate-400">
                Warga umum tidak perlu login untuk menggunakan <a href="/warga" class="text-emerald-600 font-bold hover:underline">Tombol Panic</a> atau <a href="/warga#form-tamu-warga" class="text-emerald-600 font-bold hover:underline">Buku Tamu</a>.
            </p>
        </div>

    </div>

    <!-- KARTU DEMO LOGIN 1-KLIK (UNTUK PENGUJIAN INSTAN) -->
    <div class="bg-slate-100/80 rounded-3xl p-5 border border-slate-200/80 space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Demo Login Cepat (1-Klik)</span>
            <span class="text-[10px] bg-violet-100 text-violet-700 px-2 py-0.5 rounded-md font-bold">Mode Pengujian</span>
        </div>
        <p class="text-[11px] text-slate-500">Klik tombol di bawah ini untuk langsung masuk tanpa mengetik kredensial:</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
            
            <!-- 1. Petugas Ronda -->
            <a href="{{ route('login.demo', 'petugas_ronda') }}" class="p-2.5 rounded-xl bg-white hover:bg-violet-50 border border-slate-200/80 hover:border-violet-300 transition flex items-center gap-2.5 text-xs shadow-2xs group">
                <div class="w-7 h-7 rounded-lg bg-violet-100 text-violet-700 font-bold flex items-center justify-center shrink-0">
                    🔦
                </div>
                <div>
                    <p class="font-bold text-slate-900 group-hover:text-violet-700">Petugas Ronda</p>
                    <p class="text-[10px] text-slate-400">Buka Scanner QR Patroli</p>
                </div>
            </a>

            <!-- 2. Pengurus RW -->
            <a href="{{ route('login.demo', 'rw') }}" class="p-2.5 rounded-xl bg-white hover:bg-indigo-50 border border-slate-200/80 hover:border-indigo-300 transition flex items-center gap-2.5 text-xs shadow-2xs group">
                <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center shrink-0">
                    🛡️
                </div>
                <div>
                    <p class="font-bold text-slate-900 group-hover:text-indigo-700">Pengurus RW (Admin)</p>
                    <p class="text-[10px] text-slate-400">Command Center & Heatmap</p>
                </div>
            </a>

            <!-- 3. Ketua RT 01 -->
            <a href="{{ route('login.demo', 'rt') }}" class="p-2.5 rounded-xl bg-white hover:bg-blue-50 border border-slate-200/80 hover:border-blue-300 transition flex items-center gap-2.5 text-xs shadow-2xs group">
                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center shrink-0">
                    🏢
                </div>
                <div>
                    <p class="font-bold text-slate-900 group-hover:text-blue-700">Ketua RT 01</p>
                    <p class="text-[10px] text-slate-400">Validasi Tamu 2x24h</p>
                </div>
            </a>

            <!-- 4. Bhabinkamtibmas -->
            <a href="{{ route('login.demo', 'bhabin') }}" class="p-2.5 rounded-xl bg-white hover:bg-amber-50 border border-slate-200/80 hover:border-amber-300 transition flex items-center gap-2.5 text-xs shadow-2xs group">
                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 font-bold flex items-center justify-center shrink-0">
                    👮
                </div>
                <div>
                    <p class="font-bold text-slate-900 group-hover:text-amber-700">Bhabinkamtibmas</p>
                    <p class="text-[10px] text-slate-400">Monitoring Kamtibmas</p>
                </div>
            </a>

            <!-- 5. Nakes Puskesmas -->
            <a href="{{ route('login.demo', 'nakes') }}" class="p-2.5 rounded-xl bg-white hover:bg-teal-50 border border-slate-200/80 hover:border-teal-300 transition flex items-center gap-2.5 text-xs shadow-2xs group">
                <div class="w-7 h-7 rounded-lg bg-teal-100 text-teal-700 font-bold flex items-center justify-center shrink-0">
                    🩺
                </div>
                <div>
                    <p class="font-bold text-slate-900 group-hover:text-teal-700">Nakes Puskesmas</p>
                    <p class="text-[10px] text-slate-400">Respon Medis Darurat</p>
                </div>
            </a>

            <!-- 6. Warga Terverifikasi NIK -->
            <a href="{{ route('login.demo', 'warga') }}" class="p-2.5 rounded-xl bg-emerald-50/80 hover:bg-emerald-100 border border-emerald-200 hover:border-emerald-300 transition flex items-center gap-2.5 text-xs shadow-2xs group">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center shrink-0">
                    🏠
                </div>
                <div>
                    <p class="font-bold text-emerald-900 group-hover:text-emerald-950 flex items-center gap-1">
                        <span>Warga Terverifikasi</span>
                        <span class="text-[9px] bg-emerald-200 text-emerald-900 px-1 rounded font-black">✓ NIK</span>
                    </p>
                    <p class="text-[10px] text-emerald-700">Budi Santoso &bull; Kentongan Aktif</p>
                </div>
            </a>

            <!-- 7. Warga Belum Verifikasi NIK (Untuk Pengujian) -->
            <a href="{{ route('login.demo', 'unverified') }}" class="p-2.5 rounded-xl bg-amber-50/80 hover:bg-amber-100 border border-amber-200 hover:border-amber-300 transition flex items-center gap-2.5 text-xs shadow-2xs group sm:col-span-2">
                <div class="w-7 h-7 rounded-lg bg-amber-500 text-white font-bold flex items-center justify-center shrink-0">
                    ⚠️
                </div>
                <div>
                    <p class="font-bold text-amber-900 group-hover:text-amber-950 flex items-center gap-1">
                        <span>Warga Belum Verifikasi NIK</span>
                        <span class="text-[9px] bg-amber-200 text-amber-900 px-1 rounded font-black">Uji Kunci</span>
                    </p>
                    <p class="text-[10px] text-amber-700">Doni &bull; Untuk menguji tombol kentongan terkunci karena NIK belum diverifikasi</p>
                </div>
            </a>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    (function initLoginDevice() {
        let deviceId = localStorage.getItem('jagawarga_device_id');
        if (!deviceId) {
            deviceId = 'dev_' + ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
            localStorage.setItem('jagawarga_device_id', deviceId);
        }
        const devField = document.getElementById('hidden-login-device-id');
        if (devField) devField.value = deviceId;
        document.cookie = "jagawarga_device_id=" + deviceId + "; path=/; max-age=" + (60 * 60 * 24 * 365) + "; SameSite=Lax";
    })();

    function togglePasswordVisibility() {
        const passInput = document.getElementById('password');
        const eyeText = document.getElementById('eye-text');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeText.innerText = 'Sembunyikan';
        } else {
            passInput.type = 'password';
            eyeText.innerText = 'Lihat';
        }
    }
</script>
@endpush
