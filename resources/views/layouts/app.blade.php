<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JagaWarga RW - Sistem Keamanan Warga Digital')</title>

    <!-- PWA & Mobile Web App Meta Tags -->
    <meta name="description" content="Sistem Integrasi Keamanan Warga Digital, Kentongan Online & Presensi Patroli Ronda Checkpoint">
    <meta name="theme-color" content="#059669">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="JagaWarga">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/icons/icon.svg">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Styles & Scripts with Tailwind CSS v4 -->
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Fallback Tailwind CSS via CDN for direct browser preview -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            @keyframes radar-pulse {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 24px rgba(225, 29, 72, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
            }
            @keyframes beacon-green {
                0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.6); }
                70% { transform: scale(1); box-shadow: 0 0 0 14px rgba(5, 150, 105, 0); }
                100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
            }
            .radar-beacon { animation: beacon-green 2s infinite cubic-bezier(0.4, 0, 0.6, 1); }
            .panic-pulse { animation: radar-pulse 1.8s infinite cubic-bezier(0.4, 0, 0.6, 1); }
            .glass-panel { background: rgba(255, 255, 255, 0.90); backdrop-filter: blur(12px); }
        </style>
    @endif

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col text-slate-800 antialiased selection:bg-emerald-500 selection:text-white pb-20 md:pb-8">

    <!-- Status Bar Offline Indicator -->
    <div id="offline-toast" class="hidden fixed top-0 left-0 right-0 z-50 bg-amber-600 text-white text-xs font-semibold py-1.5 px-4 text-center shadow-md">
        ⚠️ Mode Offline: Koneksi internet terputus. Data akan disinkronkan saat online kembali.
    </div>

    <!-- Active Emergency Broadcast Banner (Tampil otomatis saat Panic Alert aktif) -->
    <div id="emergency-banner" class="hidden fixed top-0 left-0 right-0 z-40 bg-rose-600 text-white shadow-xl border-b-2 border-rose-800 transition-all duration-300">
        <div class="max-w-5xl mx-auto px-4 py-2.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-white animate-ping"></span>
                <span class="text-xs md:text-sm font-bold tracking-wide uppercase">KENTONGAN DARURAT BERBUNYI!</span>
                <span id="emergency-details" class="text-xs bg-rose-700/80 px-2 py-0.5 rounded text-rose-100 font-medium hidden md:inline">
                    RT 01 Blok A No. 12
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="stopEmergencySound()" class="px-2.5 py-1 text-xs font-bold bg-white text-rose-700 rounded-lg shadow hover:bg-rose-50 transition cursor-pointer">
                    Mute Suara
                </button>
                <button onclick="dismissEmergencyBanner()" class="text-white/80 hover:text-white text-lg leading-none p-1 cursor-pointer" aria-label="Tutup">
                    &times;
                </button>
            </div>
        </div>
    </div>

    <!-- Top Header Navigation -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 p-0.5 shadow-md shadow-emerald-700/20 group-hover:scale-105 transition duration-200">
                    <div class="w-full h-full rounded-[10px] bg-emerald-700/40 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-lg text-slate-900 tracking-tight">JagaWarga</span>
                        <span class="text-xs font-bold px-1.5 py-0.5 rounded bg-violet-100 text-violet-700 border border-violet-200">RW 02</span>
                    </div>
                    <p class="text-[11px] font-medium text-emerald-700 leading-none">Keamanan Digital Warga</p>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-1 bg-slate-100/80 p-1 rounded-2xl border border-slate-200/60 text-xs font-bold">
                <a href="/" class="px-3 py-1.5 rounded-xl {{ request()->is('/') ? 'bg-white text-emerald-800 shadow-xs' : 'text-slate-600 hover:text-slate-900' }} transition">
                    Beranda
                </a>
                <a href="/warga" class="px-3 py-1.5 rounded-xl {{ request()->is('warga*') ? 'bg-white text-emerald-800 shadow-xs' : 'text-slate-600 hover:text-slate-900' }} transition">
                    Warga & Lapor
                </a>
                <a href="/ronda" class="px-3 py-1.5 rounded-xl {{ request()->is('ronda*') ? 'bg-white text-violet-800 shadow-xs' : 'text-slate-600 hover:text-slate-900' }} transition">
                    Petugas Ronda
                </a>
                <a href="/dashboard" class="px-3 py-1.5 rounded-xl {{ request()->is('dashboard*') ? 'bg-white text-indigo-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }} transition">
                    Dashboard RW
                </a>
            </nav>

            <!-- Status Siaga & Quick Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                
                <!-- Status Lingkungan Beacon -->
                <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-xs font-semibold text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 radar-beacon"></span>
                    <span>Wilayah Kondusif</span>
                </div>

                <!-- Tombol Uji Suara Kentongan -->
                <button onclick="toggleKentonganSound()" id="sound-btn" title="Uji Suara Kentongan Digital" class="p-2 rounded-xl text-slate-600 hover:text-violet-700 hover:bg-violet-50 transition border border-slate-200/80 cursor-pointer">
                    <svg id="sound-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    </svg>
                </button>

                <!-- Tombol Install PWA (Hanya muncul jika browser mendukung) -->
                <button id="pwa-install-btn" class="hidden items-center gap-1.5 px-3 py-1.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Pasang App</span>
                </button>

                <!-- Autentikasi Pengguna: Login / Akun Aktif & Logout -->
                @guest
                    <a href="{{ route('login') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200/80 text-xs font-bold transition cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Masuk Petugas</span>
                    </a>
                @else
                    <div class="flex items-center gap-2">
                        <div class="hidden sm:flex items-center gap-2 pl-2 pr-3 py-1 rounded-xl bg-slate-100/90 border border-slate-200 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-bold text-slate-800">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-violet-100 text-violet-700 font-bold uppercase">{{ Auth::user()->role_badge }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar dari Akun" class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200/80 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endguest

            </div>
        </div>
    </header>

    <!-- Main Dynamic Content -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-5">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Bar (App Shell PWA) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-lg border-t border-slate-200/80 px-2 py-1.5 shadow-lg">
        <div class="max-w-md mx-auto grid grid-cols-5 gap-1 text-center">
            
            <!-- 1. Beranda / Panic -->
            <a href="/" class="flex flex-col items-center py-1 {{ request()->is('/') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-700' }} transition group">
                <div class="p-1 rounded-xl group-hover:bg-emerald-50 transition">
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5">Beranda</span>
            </a>

            <!-- 2. Modul Warga & Lapor -->
            <a href="/warga" class="flex flex-col items-center py-1 {{ request()->is('warga*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-700' }} transition group">
                <div class="p-1 rounded-xl group-hover:bg-emerald-50 transition">
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5">Warga</span>
            </a>

            <!-- 3. Presensi Ronda (QR Scan) -->
            <a href="/ronda" class="flex flex-col items-center py-1 {{ request()->is('ronda*') ? 'text-violet-700 font-bold' : 'text-slate-500 hover:text-violet-700' }} transition group">
                <div class="p-1 rounded-xl group-hover:bg-violet-50 transition">
                    <svg class="w-5 h-5 mx-auto text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <span class="text-[10px] font-semibold text-violet-700 leading-tight mt-0.5">Patroli QR</span>
            </a>

            <!-- 4. Buku Tamu (2x24h) -->
            <a href="/warga#form-tamu-warga" class="flex flex-col items-center py-1 text-slate-500 hover:text-blue-700 transition group">
                <div class="p-1 rounded-xl group-hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5">Buku Tamu</span>
            </a>

            <!-- 5. Dashboard RW / Peta -->
            <a href="/dashboard" class="flex flex-col items-center py-1 {{ request()->is('dashboard*') ? 'text-indigo-700 font-bold' : 'text-slate-500 hover:text-indigo-700' }} transition group">
                <div class="p-1 rounded-xl group-hover:bg-indigo-50 transition">
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5">Dashboard RW</span>
            </a>

        </div>
    </nav>

    <!-- Footer untuk Desktop -->
    <footer class="hidden md:block border-t border-slate-200 mt-auto py-6 bg-white/50 text-xs text-slate-500 text-center">
        <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-semibold text-slate-700">JagaWarga RW 02</span> &mdash; Sistem Integrasi Keamanan Warga Digital
            </div>
            <div>
                Kolaborasi Warga, Pos Ronda, Kelurahan & Bhabinkamtibmas
            </div>
        </div>
    </footer>

    <!-- Audio Synthesizer Kentongan Online (HTML5 Web Audio API) -->
    <script>
        // Web Audio Synthesizer untuk Suara Kentongan Bambu/Kayu Khas Pos Ronda
        let audioCtx = null;
        let kentonganInterval = null;
        let isPlayingAudio = false;

        function getAudioContext() {
            if (!audioCtx) {
                const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                audioCtx = new AudioContextClass();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            return audioCtx;
        }

        // Memainkan 1 ketukan kentongan kayu (wood-block resonance)
        function playKentonganKnock(frequency = 580, duration = 0.12) {
            const ctx = getAudioContext();
            const now = ctx.currentTime;

            // Oscillator utama (resonansi frekuensi kayu)
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(frequency, now);
            osc.frequency.exponentialRampToValueAtTime(frequency * 0.4, now + duration);

            gain.gain.setValueAtTime(0.8, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + duration);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now);
            osc.stop(now + duration);
        }

        // Memainkan ritme Kentongan Darurat (Titik-Titik Doro / Bahaya Cepat)
        function startKentonganAlarm() {
            if (isPlayingAudio) return;
            isPlayingAudio = true;
            document.getElementById('sound-btn').classList.add('bg-rose-100', 'text-rose-700');

            let beat = 0;
            kentonganInterval = setInterval(() => {
                // Pola ketukan kentongan bahaya: tong-tong-tong cepat
                playKentonganKnock(beat % 2 === 0 ? 640 : 540, 0.09);
                beat++;
            }, 160);
        }

        function stopEmergencySound() {
            if (kentonganInterval) {
                clearInterval(kentonganInterval);
                kentonganInterval = null;
            }
            isPlayingAudio = false;
            document.getElementById('sound-btn').classList.remove('bg-rose-100', 'text-rose-700');
        }

        function toggleKentonganSound() {
            if (isPlayingAudio) {
                stopEmergencySound();
            } else {
                startKentonganAlarm();
                setTimeout(() => {
                    // Demo auto-stop setelah 4 detik jika hanya preview
                    if (isPlayingAudio && document.getElementById('emergency-banner').classList.contains('hidden')) {
                        stopEmergencySound();
                    }
                }, 4000);
            }
        }

        function triggerEmergencyAlert(detail = 'RT 01 Blok A No. 12') {
            const banner = document.getElementById('emergency-banner');
            const detailsSpan = document.getElementById('emergency-details');
            if (detailsSpan) detailsSpan.innerText = detail;
            banner.classList.remove('hidden');
            startKentonganAlarm();
        }

        function dismissEmergencyBanner() {
            document.getElementById('emergency-banner').classList.add('hidden');
            stopEmergencySound();
        }

        // Role Switcher Interaction
        function toggleRoleMenu() {
            const menu = document.getElementById('role-dropdown');
            menu.classList.toggle('hidden');
        }

        function switchRole(role) {
            localStorage.setItem('jagawarga_role', role);
            updateRoleUI(role);
            toggleRoleMenu();

            // Dispatch event agar halaman dinamis dapat merespons perubahan peran
            window.dispatchEvent(new CustomEvent('role-changed', { detail: { role } }));
        }

        function updateRoleUI(role) {
            const label = document.getElementById('active-role-label');
            const avatar = document.getElementById('role-avatar-letter');
            
            const roleConfig = {
                'warga': { name: 'Peran: Warga', letter: 'W', color: 'bg-emerald-500' },
                'petugas_ronda': { name: 'Peran: Ronda', letter: 'R', color: 'bg-violet-600' },
                'rt': { name: 'Peran: RT 01', letter: 'RT', color: 'bg-blue-600' },
                'rw': { name: 'Peran: RW (Admin)', letter: 'RW', color: 'bg-indigo-600' },
                'bhabinkamtibmas': { name: 'Peran: Bhabin', letter: 'BK', color: 'bg-amber-600' }
            };

            const config = roleConfig[role] || roleConfig['warga'];
            if (label) label.innerText = config.name;
            if (avatar) avatar.innerText = config.letter;
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', (e) => {
            const button = document.getElementById('role-menu-button');
            const dropdown = document.getElementById('role-dropdown');
            if (button && dropdown && !button.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Initialize saved role
        const savedRole = localStorage.getItem('jagawarga_role') || 'warga';
        updateRoleUI(savedRole);

        // Network Status Check
        window.addEventListener('online', () => {
            document.getElementById('offline-toast').classList.add('hidden');
        });
        window.addEventListener('offline', () => {
            document.getElementById('offline-toast').classList.remove('hidden');
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('JagaWarga PWA ServiceWorker aktif:', reg.scope))
                    .catch((err) => console.log('ServiceWorker registrasi gagal:', err));
            });
        }

        // PWA Install Prompt Handler
        let deferredPrompt;
        const installBtn = document.getElementById('pwa-install-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installBtn) {
                installBtn.classList.remove('hidden');
                installBtn.classList.add('flex');
            }
        });

        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`User respon install PWA: ${outcome}`);
                    deferredPrompt = null;
                    installBtn.classList.add('hidden');
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
