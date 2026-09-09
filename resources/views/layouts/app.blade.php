<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Arum Smart - Sistem Keamanan Warga Digital')</title>

    <!-- PWA & Mobile Web App Meta Tags -->
    <meta name="description" content="Sistem Integrasi Keamanan Warga Digital, Kentongan Online & Presensi Patroli Ronda Checkpoint">
    <meta name="theme-color" content="#059669">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ArumSmart">
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

    <!-- PWA Emergency Notification Prompt Banner -->
    <div id="pwa-notif-prompt" class="hidden fixed top-0 left-0 right-0 z-45 bg-gradient-to-r from-amber-600 via-rose-600 to-red-600 text-white shadow-xl border-b border-rose-700 transition-all duration-300">
        <div class="max-w-5xl mx-auto px-4 py-2.5 flex flex-col sm:flex-row items-center justify-between gap-2.5">
            <div class="flex items-center gap-2.5 text-xs sm:text-sm font-semibold">
                <span class="text-lg animate-bounce">🔔</span>
                <span><strong>Aktifkan Notifikasi Kentongan PWA:</strong> Agar HP/perangkat Anda langsung membunyikan sirine dan bergetar saat warga membunyikan kentongan darurat RW 02!</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="requestPwaNotificationPermission()" class="px-3.5 py-1.5 bg-white text-rose-700 font-extrabold text-xs rounded-xl shadow-md hover:bg-rose-50 transition cursor-pointer flex items-center gap-1.5">
                    <span>Izinkan Notifikasi</span>
                </button>
                <button type="button" onclick="dismissPwaNotifPrompt()" class="text-white/80 hover:text-white text-lg leading-none p-1 cursor-pointer" title="Tutup">
                    &times;
                </button>
            </div>
        </div>
    </div>

    <!-- Active Emergency Broadcast Banner (Tampil otomatis saat Panic Alert aktif) -->
    <div id="emergency-banner" class="hidden fixed top-0 left-0 right-0 z-40 bg-rose-600 text-white shadow-xl border-b-2 border-rose-800 transition-all duration-300">
        <div class="max-w-5xl mx-auto px-4 py-2.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="w-3 h-3 rounded-full bg-white animate-ping"></span>
                <span id="emergency-banner-title" class="text-xs md:text-sm font-black tracking-wide uppercase">🚨 KENTONGAN DARURAT BERBUNYI!</span>
                <span id="emergency-details" class="text-xs bg-white/20 px-2 py-0.5 rounded-lg text-white font-medium">
                    RT 01 Blok A No. 12
                </span>
                <span id="emergency-sound-rhythm" class="text-[11px] bg-black/25 px-2.5 py-0.5 rounded-md text-amber-200 font-semibold hidden md:inline">
                    Ketukan Cepat Doro Muluk
                </span>
            </div>
            <div class="flex items-center gap-2 self-end sm:self-auto">
                <button onclick="stopEmergencySound()" class="px-2.5 py-1 text-xs font-bold bg-white text-slate-800 rounded-lg shadow hover:bg-slate-100 transition cursor-pointer flex items-center gap-1">
                    🔇 <span>Mute Suara</span>
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
                        <span class="font-extrabold text-lg text-slate-900 tracking-tight">ArumSmart</span>
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
                @auth
                    @if(Auth::user()->isPetugasRonda() || Auth::user()->isPengurus())
                    <a href="/ronda" class="px-3 py-1.5 rounded-xl {{ request()->is('ronda*') ? 'bg-white text-violet-800 shadow-xs' : 'text-slate-600 hover:text-slate-900' }} transition">
                        Petugas Ronda
                    </a>
                    @endif

                    @if(Auth::user()->isPengurus() || Auth::user()->isNakes())
                    <a href="/dashboard" class="px-3 py-1.5 rounded-xl {{ request()->is('dashboard*') ? 'bg-white text-indigo-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }} transition">
                        Dashboard RW
                    </a>
                    <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-xl {{ request()->is('users*') ? 'bg-white text-blue-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }} transition">
                        Manajemen User
                    </a>
                    <a href="{{ route('device.requests.index') }}" class="px-3 py-1.5 rounded-xl {{ request()->is('device-requests*') ? 'bg-white text-amber-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }} transition flex items-center gap-1.5">
                        <span>Perangkat</span>
                        @php $pendingDevs = \App\Models\DeviceResetRequest::where('status', 'pending')->count(); @endphp
                        @if($pendingDevs > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-black animate-pulse">{{ $pendingDevs }}</span>
                        @endif
                    </a>
                    <a href="{{ route('settings.index') }}" class="px-3 py-1.5 rounded-xl {{ request()->is('settings*') ? 'bg-white text-violet-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }} transition">
                        Pengaturan
                    </a>
                    @endif
                @endauth
            </nav>

            <!-- Status Siaga & Quick Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                
                <!-- Status Lingkungan Beacon -->
                <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-xs font-semibold text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 radar-beacon"></span>
                    <span>Wilayah Kondusif</span>
                </div>

                <!-- Tombol Uji Suara Kentongan dengan Dropdown Pilihan Kategori -->
                <div class="relative">
                    <button onclick="toggleSoundMenu()" id="sound-btn" title="Uji Suara Kentongan Digital per Kategori" class="p-2 rounded-xl text-slate-600 hover:text-violet-700 hover:bg-violet-50 transition border border-slate-200/80 cursor-pointer flex items-center gap-1.5">
                        <svg id="sound-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        </svg>
                        <span class="text-[11px] font-extrabold hidden sm:inline text-slate-700">Tes Suara</span>
                    </button>

                    <!-- Dropdown Pilihan Suara Kentongan -->
                    <div id="sound-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-200/80 py-2 z-50 text-xs">
                        <div class="px-3.5 py-1.5 border-b border-slate-100 flex items-center justify-between">
                            <span class="font-extrabold text-[11px] text-slate-800">Pilih Ciri Suara Kentongan</span>
                            <button type="button" onclick="stopEmergencySound(); document.getElementById('sound-dropdown').classList.add('hidden')" class="text-[10px] text-rose-600 font-extrabold hover:underline cursor-pointer">Stop Audio</button>
                        </div>
                        <div class="p-1.5 space-y-1">
                            <button type="button" onclick="testSoundCategory('pencurian')" class="w-full text-left p-2 rounded-xl hover:bg-rose-50 flex items-start gap-2.5 transition cursor-pointer">
                                <span class="text-base">🚨</span>
                                <div>
                                    <span class="font-bold text-slate-900 block text-[11px]">Maling / Curanmor</span>
                                    <span class="text-[10px] text-slate-500 leading-tight block">Ketukan Doro Muluk cepat bertubi-tubi & sirene maling</span>
                                </div>
                            </button>
                            <button type="button" onclick="testSoundCategory('kebakaran')" class="w-full text-left p-2 rounded-xl hover:bg-orange-50 flex items-start gap-2.5 transition cursor-pointer">
                                <span class="text-base">🔥</span>
                                <div>
                                    <span class="font-bold text-slate-900 block text-[11px]">Bahaya Kebakaran</span>
                                    <span class="text-[10px] text-slate-500 leading-tight block">Titir ganda (Tang-Tang...) & sirene damkar melolong</span>
                                </div>
                            </button>
                            <button type="button" onclick="testSoundCategory('medis')" class="w-full text-left p-2 rounded-xl hover:bg-blue-50 flex items-start gap-2.5 transition cursor-pointer">
                                <span class="text-base">🚑</span>
                                <div>
                                    <span class="font-bold text-slate-900 block text-[11px]">Darurat Medis / Ambulans</span>
                                    <span class="text-[10px] text-slate-500 leading-tight block">Sirene dua nada (WEE-WOO) & ketukan teratur</span>
                                </div>
                            </button>
                            <button type="button" onclick="testSoundCategory('lainnya')" class="w-full text-left p-2 rounded-xl hover:bg-amber-50 flex items-start gap-2.5 transition cursor-pointer">
                                <span class="text-base">⚠️</span>
                                <div>
                                    <span class="font-bold text-slate-900 block text-[11px]">Siaga Lingkungan / Bencana</span>
                                    <span class="text-[10px] text-slate-500 leading-tight block">Ketukan siaga poskamling & nada peringatan</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tombol Notifikasi PWA (Status & Uji Coba) -->
                <button id="pwa-notif-btn" type="button" onclick="handlePwaNotifBtnClick()" class="hidden items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 border border-slate-200 text-xs font-bold transition cursor-pointer text-slate-700" title="Status Notifikasi PWA">
                    <span id="pwa-notif-status-dot" class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span id="pwa-notif-btn-text">Notif PWA</span>
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
                        @php
                            $userRoles = Auth::user()->roles;
                        @endphp
                        <!-- Dropdown Switcher Peran Aktif -->
                        <div class="relative">
                            <button id="role-menu-button" type="button" onclick="toggleRoleMenu()" class="flex items-center gap-2 pl-2.5 pr-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 border border-slate-200 text-xs transition cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="font-bold text-slate-800 hidden sm:inline">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-violet-100 text-violet-700 font-extrabold uppercase border border-violet-200 flex items-center gap-1">
                                    <span>{{ Auth::user()->role_badge }}</span>
                                    @if($userRoles->count() > 1)
                                    <svg class="w-3 h-3 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    @endif
                                </span>
                            </button>

                            @if($userRoles->count() > 1)
                            <div id="role-dropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-200/80 py-2 z-50 text-xs animate-in fade-in">
                                <div class="px-3.5 py-1.5 border-b border-slate-100">
                                    <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wider">Peran Anda (Multi-Role)</span>
                                    <span class="text-xs font-bold text-slate-700">Pilih untuk beralih mode:</span>
                                </div>
                                <div class="p-1.5 space-y-1">
                                    @foreach($userRoles as $r)
                                    <form action="{{ route('role.switch') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $r->name }}">
                                        <button type="submit" class="w-full text-left p-2 rounded-xl transition flex items-center justify-between cursor-pointer {{ session('active_role') === $r->name ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200' : 'hover:bg-slate-50 text-slate-700' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="text-base">{{ $r->icon }}</span>
                                                <div>
                                                    <span class="block text-xs">{{ $r->display_name }}</span>
                                                    @if(session('active_role') === $r->name)
                                                    <span class="text-[9px] text-emerald-600 font-semibold block">Peran Aktif Saat Ini</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(session('active_role') === $r->name)
                                            <span class="text-emerald-600 text-xs font-bold">✓</span>
                                            @endif
                                        </button>
                                    </form>
                                    @endforeach
                                </div>
                                <div class="pt-1 border-t border-slate-100 px-2">
                                    <a href="{{ route('role.select') }}" class="block text-center py-1 text-[11px] font-bold text-violet-700 hover:underline">
                                        Buka Layar Pemilihan Peran
                                    </a>
                                </div>
                            </div>
                            @endif
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
        <div class="max-w-md mx-auto flex items-center justify-around text-center">
            
            <!-- 1. Beranda / Panic -->
            <a href="/" class="flex-1 flex flex-col items-center py-1 {{ request()->is('/') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-700' }} transition group">
                <div class="p-1 rounded-xl group-hover:bg-emerald-50 transition">
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5">Beranda</span>
            </a>

            <!-- 2. Modul Warga & Lapor -->
            <a href="/warga" class="flex-1 flex flex-col items-center py-1 {{ request()->is('warga*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-700' }} transition group">
                <div class="p-1 rounded-xl group-hover:bg-emerald-50 transition">
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5">Warga</span>
            </a>

            <!-- 3. Buku Tamu (2x24h) -->
            <a href="/warga#form-tamu-warga" class="flex-1 flex flex-col items-center py-1 text-slate-500 hover:text-blue-700 transition group">
                <div class="p-1 rounded-xl group-hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5">Buku Tamu</span>
            </a>

            @auth
                <!-- 4. Petugas Ronda (Role: petugas_ronda, rt, rw, bhabinkamtibmas) -->
                @if(Auth::user()->isPetugasRonda() || Auth::user()->isPengurus())
                <a href="/ronda" class="flex-1 flex flex-col items-center py-1 {{ request()->is('ronda*') ? 'text-violet-700 font-bold' : 'text-slate-500 hover:text-violet-700' }} transition group">
                    <div class="p-1 rounded-xl group-hover:bg-violet-50 transition">
                        <svg class="w-5 h-5 mx-auto text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-semibold text-violet-700 leading-tight mt-0.5">Patroli QR</span>
                </a>
                @endif

                <!-- 5. Dashboard RW (Role: rt, rw, bhabinkamtibmas, nakes_puskesmas) -->
                @if(Auth::user()->isPengurus() || Auth::user()->isNakes())
                <a href="/dashboard" class="flex-1 flex flex-col items-center py-1 {{ request()->is('dashboard*') ? 'text-indigo-700 font-bold' : 'text-slate-500 hover:text-indigo-700' }} transition group">
                    <div class="p-1 rounded-xl group-hover:bg-indigo-50 transition">
                        <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-medium leading-tight mt-0.5">Dashboard</span>
                </a>

                <!-- 6. Kelola Pengguna (Role: rt, rw, bhabinkamtibmas) -->
                <a href="{{ route('users.index') }}" class="flex-1 flex flex-col items-center py-1 {{ request()->is('users*') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-700' }} transition group">
                    <div class="p-1 rounded-xl group-hover:bg-blue-50 transition">
                        <svg class="w-5 h-5 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-medium leading-tight mt-0.5">User</span>
                </a>

                <!-- 7. Pengaturan Sistem (Role: rt, rw, bhabinkamtibmas) -->
                <a href="{{ route('settings.index') }}" class="flex-1 flex flex-col items-center py-1 {{ request()->is('settings*') ? 'text-violet-700 font-bold' : 'text-slate-500 hover:text-violet-700' }} transition group">
                    <div class="p-1 rounded-xl group-hover:bg-violet-50 transition">
                        <svg class="w-5 h-5 mx-auto text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-medium leading-tight mt-0.5">Setting</span>
                </a>
                @endif
            @else
                <!-- Tombol Masuk Petugas untuk Publik / Tamu di HP -->
                <a href="{{ route('login') }}" class="flex-1 flex flex-col items-center py-1 text-slate-500 hover:text-emerald-700 transition group">
                    <div class="p-1 rounded-xl group-hover:bg-emerald-50 transition">
                        <svg class="w-5 h-5 mx-auto text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-medium leading-tight mt-0.5">Masuk</span>
                </a>
            @endauth

        </div>
    </nav>

    <!-- Footer untuk Desktop -->
    <footer class="hidden md:block border-t border-slate-200 mt-auto py-6 bg-white/50 text-xs text-slate-500 text-center">
        <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-semibold text-slate-700">ArumSmart RW 02</span> &mdash; Sistem Integrasi Keamanan Warga Digital
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
        let activeEmergencyCategory = 'pencurian';

        // Konfigurasi Ritme Tradisional & Sirene Per Kategori Bahaya
        const categoryAudioConfig = {
            'pencurian': {
                name: 'MALING / CURANMOR',
                icon: '🚨',
                bgClass: 'bg-rose-600',
                borderClass: 'border-rose-800',
                btnColorClass: 'bg-rose-600',
                rhythmDesc: 'Ketukan Bertubi-tubi Sangat Cepat (Doro Muluk) & Sirene Maling',
                voiceAlert: 'Perhatian! Ada maling atau pencurian di lingkungan warga! Warga segera siaga kepung lokasi!',
                intervalMs: 120, // Sangat cepat & rapat
                playBeat: (beat) => {
                    // Ketukan kayu sangat cepat & tajam (720Hz & 600Hz bergantian)
                    const freq = (beat % 2 === 0) ? 720 : 600;
                    playKentonganKnock(freq, 0.08, 0.9);

                    // Tambahkan suara buzzer/siren chirp tajam setiap 4 ketukan
                    if (beat % 4 === 0) {
                        playSirenTone(1100, 'sawtooth', 0.12, 0.25);
                    } else if (beat % 4 === 2) {
                        playSirenTone(880, 'sawtooth', 0.12, 0.22);
                    }
                }
            },
            'kebakaran': {
                name: 'BAHAYA KEBAKARAN',
                icon: '🔥',
                bgClass: 'bg-orange-600',
                borderClass: 'border-orange-800',
                btnColorClass: 'bg-orange-600',
                rhythmDesc: 'Ketukan Titir Ganda (Tang-Tang... Tang-Tang...) & Sirene Damkar',
                voiceAlert: 'Perhatian! Bahaya kebakaran! Bawa air dan alat pemadam, segera bantu lokasi!',
                intervalMs: 160,
                playBeat: (beat) => {
                    // Pola 4 ketukan: beat 0 dan 1 ketuk keras rangkap, beat 2 dan 3 jeda untuk sirene
                    const step = beat % 4;
                    if (step === 0 || step === 1) {
                        playKentonganKnock(680, 0.11, 0.95);
                    }

                    // Sirene damkar melolong naik-turun berkala
                    const sirenFreqs = [440, 580, 780, 960, 840, 680, 520, 460];
                    const sFreq = sirenFreqs[beat % sirenFreqs.length];
                    playSirenTone(sFreq, 'triangle', 0.22, 0.3);
                }
            },
            'medis': {
                name: 'DARURAT MEDIS / AMBULANS',
                icon: '🚑',
                bgClass: 'bg-blue-600',
                borderClass: 'border-blue-800',
                btnColorClass: 'bg-blue-600',
                rhythmDesc: 'Sirene Ambulans Dua Nada (WEE-WOO) & Ketukan Lambat',
                voiceAlert: 'Panggilan darurat medis! Pertolongan pertama dan ambulans dibutuhkan!',
                intervalMs: 360,
                playBeat: (beat) => {
                    // Nada Hi-Lo Ambulans khas Indonesia/Eropa (750Hz - 540Hz bergantian)
                    const isHi = (beat % 2 === 0);
                    playSirenTone(isHi ? 750 : 540, 'sine', 0.33, 0.35);

                    // Ketukan kentongan bulat ritmis lambat
                    if (isHi) {
                        playKentonganKnock(480, 0.16, 0.7);
                    }
                }
            },
            'lainnya': {
                name: 'PERINGATAN SIAGA LINGKUNGAN',
                icon: '⚠️',
                bgClass: 'bg-amber-600',
                borderClass: 'border-amber-800',
                btnColorClass: 'bg-amber-600',
                rhythmDesc: 'Ketukan Panggilan Siaga Pos (Tong... Tong... Tong-Tong-Tong)',
                voiceAlert: 'Perhatian! Peringatan siaga keamanan lingkungan RW 02!',
                intervalMs: 240,
                playBeat: (beat) => {
                    const step = beat % 6;
                    if (step === 0 || step === 2 || step === 4 || step === 5) {
                        playKentonganKnock(540, 0.14, 0.85);
                    }
                    if (step === 0) {
                        playSirenTone(640, 'triangle', 0.2, 0.2);
                    }
                }
            }
        };

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

        // 1. Ketukan Resonansi Kayu Kentongan
        function playKentonganKnock(frequency = 580, duration = 0.12, volume = 0.8) {
            const ctx = getAudioContext();
            const now = ctx.currentTime;

            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(frequency, now);
            osc.frequency.exponentialRampToValueAtTime(frequency * 0.35, now + duration);

            gain.gain.setValueAtTime(volume, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + duration);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now);
            osc.stop(now + duration);
        }

        // 2. Nada Sirene / Melolong untuk Pembeda Alarm
        function playSirenTone(frequency, type = 'sine', duration = 0.2, volume = 0.25) {
            const ctx = getAudioContext();
            const now = ctx.currentTime;

            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = type;
            osc.frequency.setValueAtTime(frequency, now);

            gain.gain.setValueAtTime(volume, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + duration);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now);
            osc.stop(now + duration);
        }

        // 3. Pengumuman Suara Suara Otomatis (Speech Synthesis)
        function speakVoiceAlert(text) {
            if ('speechSynthesis' in window) {
                try {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 1.05;
                    utterance.pitch = 1.1;
                    utterance.volume = 1.0;
                    window.speechSynthesis.speak(utterance);
                } catch (e) {
                    console.log('Voice announcement unsupported:', e);
                }
            }
        }

        // Memulai ritme audio spesifik per kategori bahaya
        function startKentonganAlarm(kategori = 'pencurian') {
            stopEmergencySound();
            isPlayingAudio = true;
            activeEmergencyCategory = kategori;

            const cfg = categoryAudioConfig[kategori] || categoryAudioConfig['pencurian'];

            // Bunyikan pengumuman suara bahasa Indonesia agar warga langsung tahu
            speakVoiceAlert(cfg.voiceAlert);

            // Nyalakan ketukan ritmis berulang
            let beat = 0;
            kentonganInterval = setInterval(() => {
                cfg.playBeat(beat);
                beat++;
            }, cfg.intervalMs);

            const soundBtn = document.getElementById('sound-btn');
            if (soundBtn) {
                soundBtn.classList.add('bg-rose-100', 'text-rose-700');
            }
        }

        function stopEmergencySound() {
            if (kentonganInterval) {
                clearInterval(kentonganInterval);
                kentonganInterval = null;
            }
            if ('speechSynthesis' in window) {
                try { window.speechSynthesis.cancel(); } catch(e) {}
            }
            isPlayingAudio = false;
            const soundBtn = document.getElementById('sound-btn');
            if (soundBtn) {
                soundBtn.classList.remove('bg-rose-100', 'text-rose-700');
            }
        }

        function toggleKentonganSound() {
            if (isPlayingAudio) {
                stopEmergencySound();
            } else {
                startKentonganAlarm(activeEmergencyCategory || 'pencurian');
                setTimeout(() => {
                    // Demo auto-stop setelah 4.5 detik jika hanya preview tombol di header
                    if (isPlayingAudio && document.getElementById('emergency-banner').classList.contains('hidden')) {
                        stopEmergencySound();
                    }
                }, 4500);
            }
        }

        function triggerEmergencyAlert(categoryOrDetail = 'pencurian', detailText = '') {
            let kategori = 'pencurian';
            let detail = '';

            if (typeof categoryOrDetail === 'string') {
                const lower = categoryOrDetail.toLowerCase();
                if (lower.includes('kebakaran')) kategori = 'kebakaran';
                else if (lower.includes('medis')) kategori = 'medis';
                else if (lower.includes('lainnya')) kategori = 'lainnya';
                else if (lower.includes('maling') || lower.includes('curanmor') || lower.includes('pencurian')) kategori = 'pencurian';
                else if (categoryAudioConfig[categoryOrDetail]) kategori = categoryOrDetail;

                detail = detailText || categoryOrDetail;
            }

            const cfg = categoryAudioConfig[kategori] || categoryAudioConfig['pencurian'];
            const banner = document.getElementById('emergency-banner');
            const titleEl = document.getElementById('emergency-banner-title');
            const detailsSpan = document.getElementById('emergency-details');
            const rhythmSpan = document.getElementById('emergency-sound-rhythm');

            if (banner) {
                banner.className = `fixed top-0 left-0 right-0 z-40 ${cfg.bgClass} text-white shadow-xl border-b-2 ${cfg.borderClass} transition-all duration-300`;
                if (titleEl) titleEl.innerHTML = `${cfg.icon} ${cfg.name}!`;
                if (detailsSpan) detailsSpan.innerHTML = detail;
                if (rhythmSpan) rhythmSpan.innerText = `Ciri Suara: ${cfg.rhythmDesc}`;
                banner.classList.remove('hidden');
            }

            startKentonganAlarm(kategori);
        }

        function dismissEmergencyBanner() {
            document.getElementById('emergency-banner').classList.add('hidden');
            stopEmergencySound();
        }

        // Dropdown Uji Suara Kentongan per Kategori
        function toggleSoundMenu() {
            const menu = document.getElementById('sound-dropdown');
            if (menu) menu.classList.toggle('hidden');
        }

        function testSoundCategory(kategori) {
            const menu = document.getElementById('sound-dropdown');
            if (menu) menu.classList.add('hidden');

            startKentonganAlarm(kategori);
            setTimeout(() => {
                if (isPlayingAudio && document.getElementById('emergency-banner').classList.contains('hidden')) {
                    stopEmergencySound();
                }
            }, 4500);
        }

        // Role Switcher Interaction
        function toggleRoleMenu() {
            const menu = document.getElementById('role-dropdown');
            if (menu) menu.classList.toggle('hidden');
        }

        // Global Device Initialization
        (function initAppDevice() {
            let deviceId = localStorage.getItem('jagawarga_device_id');
            if (!deviceId) {
                deviceId = 'dev_' + ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                );
                localStorage.setItem('jagawarga_device_id', deviceId);
            }
            document.cookie = "jagawarga_device_id=" + deviceId + "; path=/; max-age=" + (60 * 60 * 24 * 365) + "; SameSite=Lax";
        })();

        // Close dropdown when clicked outside
        document.addEventListener('click', (e) => {
            const roleBtn = document.getElementById('role-menu-button');
            const roleDrop = document.getElementById('role-dropdown');
            if (roleBtn && roleDrop && !roleBtn.contains(e.target) && !roleDrop.contains(e.target)) {
                roleDrop.classList.add('hidden');
            }

            const soundBtn = document.getElementById('sound-btn');
            const soundDrop = document.getElementById('sound-dropdown');
            if (soundBtn && soundDrop && !soundBtn.contains(e.target) && !soundDrop.contains(e.target)) {
                soundDrop.classList.add('hidden');
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

        // =========================================================================
        // PWA NOTIFICATION & REAL-TIME EMERGENCY BROADCAST ENGINE
        // =========================================================================
        function getDeviceId() {
            return localStorage.getItem('jagawarga_device_id') || 'dev_guest';
        }

        function isPwaMode() {
            return window.matchMedia('(display-mode: standalone)').matches ||
                   window.navigator.standalone === true ||
                   localStorage.getItem('jagawarga_pwa_installed') === 'true';
        }

        async function registerPwaDevice(installedOverride = null) {
            const deviceId = getDeviceId();
            const isInstalled = installedOverride !== null ? installedOverride : isPwaMode();
            const notifGranted = ('Notification' in window) && Notification.permission === 'granted';

            let pushSubscription = null;
            if ('serviceWorker' in navigator && 'PushManager' in window) {
                try {
                    const reg = await navigator.serviceWorker.ready;
                    const sub = await reg.pushManager.getSubscription();
                    if (sub) pushSubscription = sub.toJSON();
                } catch (e) {
                    // Push Subscription optional
                }
            }

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                await fetch('/api/pwa/register-device', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf || ''
                    },
                    body: JSON.stringify({
                        device_id: deviceId,
                        is_pwa_installed: isInstalled,
                        notification_granted: notifGranted,
                        browser_info: (navigator.userAgent || '').substring(0, 250),
                        push_subscription: pushSubscription
                    })
                });
            } catch (err) {
                console.warn('Sinkronisasi perangkat PWA ke server:', err);
            }
        }

        function updatePwaNotificationUI() {
            const promptBanner = document.getElementById('pwa-notif-prompt');
            const notifBtn = document.getElementById('pwa-notif-btn');
            const notifStatusDot = document.getElementById('pwa-notif-status-dot');
            const notifBtnText = document.getElementById('pwa-notif-btn-text');

            if (!('Notification' in window)) {
                if (promptBanner) promptBanner.classList.add('hidden');
                if (notifBtn) notifBtn.classList.add('hidden');
                return;
            }

            if (Notification.permission === 'granted') {
                if (promptBanner) promptBanner.classList.add('hidden');
                if (notifBtn) {
                    notifBtn.classList.remove('hidden');
                    notifBtn.classList.add('flex');
                    notifBtn.title = 'Notifikasi PWA Aktif - Klik untuk uji notifikasi kentongan';
                }
                if (notifStatusDot) {
                    notifStatusDot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse';
                }
                if (notifBtnText) notifBtnText.innerText = 'Notif PWA';
            } else if (Notification.permission === 'default') {
                const dismissed = sessionStorage.getItem('pwa_notif_prompt_dismissed');
                if (promptBanner && !dismissed) {
                    promptBanner.classList.remove('hidden');
                }
                if (notifBtn) {
                    notifBtn.classList.remove('hidden');
                    notifBtn.classList.add('flex');
                    notifBtn.title = 'Klik untuk aktifkan notifikasi darurat kentongan PWA';
                }
                if (notifStatusDot) {
                    notifStatusDot.className = 'w-2 h-2 rounded-full bg-amber-400';
                }
                if (notifBtnText) notifBtnText.innerText = 'Aktifkan Notif';
            } else {
                if (promptBanner) promptBanner.classList.add('hidden');
                if (notifBtn) {
                    notifBtn.classList.remove('hidden');
                    notifBtn.classList.add('flex');
                    notifBtn.title = 'Notifikasi browser diblokir. Harap izinkan melalui pengaturan browser.';
                }
                if (notifStatusDot) {
                    notifStatusDot.className = 'w-2 h-2 rounded-full bg-rose-500';
                }
                if (notifBtnText) notifBtnText.innerText = 'Notif Diblokir';
            }
        }

        async function requestPwaNotificationPermission() {
            if (!('Notification' in window)) {
                alert('Browser ini tidak mendukung Web Notification API.');
                return;
            }

            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    sessionStorage.removeItem('pwa_notif_prompt_dismissed');
                    updatePwaNotificationUI();
                    await registerPwaDevice();

                    displaySystemKentonganNotification({
                        id: 'welcome',
                        kategori: 'info',
                        title: '🟢 Notifikasi Kentongan PWA Aktif!',
                        body: 'Perangkat Anda kini terhubung ke sistem Kentongan Online RW 02. Anda akan menerima notifikasi darurat seketika saat kentongan dibunyikan oleh warga.',
                    });
                } else if (permission === 'denied') {
                    alert('Izin notifikasi ditolak oleh peramban. Silakan izinkan akses notifikasi melalui ikon pengaturan/gembok di bilah peramban Anda.');
                    updatePwaNotificationUI();
                    await registerPwaDevice();
                }
            } catch (e) {
                console.error('Gagal meminta izin notifikasi:', e);
            }
        }

        function dismissPwaNotifPrompt() {
            const promptBanner = document.getElementById('pwa-notif-prompt');
            if (promptBanner) promptBanner.classList.add('hidden');
            sessionStorage.setItem('pwa_notif_prompt_dismissed', 'true');
        }

        function handlePwaNotifBtnClick() {
            if (Notification.permission === 'granted') {
                testPwaEmergencyNotification();
            } else {
                requestPwaNotificationPermission();
            }
        }

        function testPwaEmergencyNotification() {
            displaySystemKentonganNotification({
                id: 'test_' + Date.now(),
                kategori: 'pencurian',
                pelapor: 'Simulasi Sistem RW 02',
                catatan: 'Uji Coba Notifikasi Darurat Kentongan PWA',
                title: '🚨 [UJI COBA] KENTONGAN ONLINE RW 02',
                body: 'Sinyal darurat kentongan berhasil disiarkan dan diterima di seluruh perangkat PWA terpasang!',
            });
            triggerEmergencyAlert('pencurian', 'Uji Coba Bunyi Sirine & Notifikasi PWA');
        }

        function displaySystemKentonganNotification(alert) {
            const title = alert.title || '🚨 BAHAYA: KENTONGAN ONLINE RW 02!';
            const kategori = (alert.kategori || 'darurat').toUpperCase();
            const pelapor = alert.pelapor || 'Warga RW 02';
            const catatan = alert.catatan || 'Sinyal bahaya aktif!';
            const body = alert.body || `[${kategori}] ${pelapor}: ${catatan}. Segera cek posko & bersiap siaga!`;

            const options = {
                body: body,
                icon: '/icons/icon.svg',
                badge: '/icons/icon.svg',
                vibrate: [500, 200, 500, 200, 500, 200, 1000],
                tag: 'kentongan-darurat-' + (alert.id || Date.now()),
                renotify: true,
                requireInteraction: true,
                data: {
                    url: '/#panic-button',
                    alertId: alert.id,
                    kategori: alert.kategori
                },
                actions: [
                    { action: 'open', title: '🚨 Buka Lokasi & Siaga' },
                    { action: 'dismiss', title: 'Tutup' }
                ]
            };

            // Kirim ke Service Worker jika aktif (memiliki hak akses system tray & background notification)
            if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({
                    type: 'SHOW_PANIC_NOTIFICATION',
                    payload: {
                        title: title,
                        body: body,
                        alertId: alert.id,
                        kategori: alert.kategori,
                        options: options
                    }
                });
            } else if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const notif = new Notification(title, options);
                    notif.onclick = function () {
                        window.focus();
                        this.close();
                    };
                } catch (e) {
                    console.warn('Fallback Notification window error:', e);
                }
            }
        }

        let lastProcessedPanicId = parseInt(localStorage.getItem('jagawarga_last_panic_id') || '0');

        function processIncomingEmergencyAlert(alert) {
            if (!alert || !alert.id) return;
            const alertId = parseInt(alert.id);
            if (alertId <= lastProcessedPanicId) return;

            // Catat ID kejadian ini agar tidak memicu notifikasi berulang
            lastProcessedPanicId = alertId;
            localStorage.setItem('jagawarga_last_panic_id', lastProcessedPanicId);

            console.log('🚨 Sinyal Kentongan Darurat Diterima:', alert);

            // 1. Tampilkan Notifikasi Sistem PWA
            if ('Notification' in window && Notification.permission === 'granted') {
                displaySystemKentonganNotification(alert);
            }

            // 2. Bunyikan Alarm Sirine Kentongan Web Audio API
            const detailText = alert.catatan ? `${alert.catatan} (Pelapor: ${alert.pelapor})` : `Pelapor: ${alert.pelapor}`;
            triggerEmergencyAlert(alert.kategori || 'pencurian', detailText);

            // 3. Tambahkan ke data riwayat tampilan tabel jika tersedia
            if (typeof tambahBarisKeDataTable === 'function') {
                tambahBarisKeDataTable(alert);
            }
        }

        function initEmergencyBroadcastSync() {
            // A. Server-Sent Events (SSE) Stream
            if (window.EventSource) {
                try {
                    const sse = new EventSource('/api/panic/stream');
                    sse.onmessage = function (e) {
                        try {
                            const data = JSON.parse(e.data);
                            if (data && data.type === 'PANIC_ALERT' && data.alert) {
                                processIncomingEmergencyAlert(data.alert);
                            }
                        } catch (err) {}
                    };
                } catch (e) {}
            }

            // B. Polling berkala (setiap 3 detik) untuk keandalan maksimal di semua perangkat
            const checkActivePanic = async () => {
                try {
                    const res = await fetch('/api/panic/latest-active');
                    if (res.ok) {
                        const data = await res.json();
                        if (data.has_active && data.alert) {
                            processIncomingEmergencyAlert(data.alert);
                        }
                    }
                } catch (e) {}
            };

            setTimeout(checkActivePanic, 1000);
            setInterval(checkActivePanic, 3000);
        }

        // PWA Service Worker Registration & Messages
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => {
                        console.log('Arum Smart PWA ServiceWorker aktif:', reg.scope);
                        registerPwaDevice();
                    })
                    .catch((err) => console.log('ServiceWorker registrasi gagal:', err));

                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data && event.data.type === 'PANIC_NOTIFICATION_CLICKED') {
                        const data = event.data.data;
                        if (data && data.kategori) {
                            triggerEmergencyAlert(data.kategori, 'Darurat terdeteksi dari notifikasi PWA');
                        }
                    }
                });
            });
        }

        // Deteksi Instalasi PWA
        window.addEventListener('appinstalled', () => {
            console.log('Aplikasi ArumSmart berhasil diinstall sebagai PWA');
            localStorage.setItem('jagawarga_pwa_installed', 'true');
            registerPwaDevice(true);
        });

        // Inisialisasi status UI notifikasi dan sync darurat
        document.addEventListener('DOMContentLoaded', () => {
            updatePwaNotificationUI();
            registerPwaDevice();
            initEmergencyBroadcastSync();
        });

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
                    if (outcome === 'accepted') {
                        localStorage.setItem('jagawarga_pwa_installed', 'true');
                        registerPwaDevice(true);
                    }
                    deferredPrompt = null;
                    installBtn.classList.add('hidden');
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
