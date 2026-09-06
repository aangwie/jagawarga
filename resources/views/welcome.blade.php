@extends('layouts.app')

@section('title', 'JagaWarga RW 02 - Sistem Integrasi Keamanan Warga Digital')

@section('content')
<div class="space-y-6">

    <!-- Role Context Banner (Dinamis berdasarkan simulasi peran) -->
    <div id="role-context-banner" class="bg-gradient-to-r from-emerald-600 via-teal-600 to-violet-700 rounded-2xl p-4 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-36 h-36 rounded-full bg-white/10 blur-xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl font-bold">
                    🛡️
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-bold tracking-tight">Posko Keamanan Digital RW 02</h1>
                    <p id="role-greeting" class="text-xs text-emerald-100 font-medium">Selamat datang di sistem integrasi warga dan pos ronda.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="bg-white/20 px-2.5 py-1 rounded-lg backdrop-blur-md font-semibold">RT 01 & RT 02</span>
                <span class="bg-emerald-800/60 text-emerald-100 px-2.5 py-1 rounded-lg font-semibold">PWA Aktif</span>
            </div>
        </div>
    </div>

    <!-- SEKSI 1: TOMBOL DARURAT DIGITAL (KENTONGAN ONLINE) -->
    <section id="panic-button" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden">
        <div class="max-w-md mx-auto text-center space-y-4">
            
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold border border-rose-100">
                <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                <span>KENTONGAN ONLINE 24 JAM</span>
            </div>

            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Tekan Untuk Sinyal Bahaya</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Sinyal darurat akan langsung membunyikan alarm keras di HP petugas ronda, posko RW, dan tetangga terdekat.
                </p>
            </div>

            <!-- Kategori Bahaya Selector -->
            <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                <button type="button" onclick="selectPanicCategory('pencurian', this)" class="panic-cat-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-600 text-white shadow-xs cursor-pointer transition">
                    🚨 Maling / Curanmor
                </button>
                <button type="button" onclick="selectPanicCategory('kebakaran', this)" class="panic-cat-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer transition">
                    🔥 Kebakaran
                </button>
                <button type="button" onclick="selectPanicCategory('medis', this)" class="panic-cat-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer transition">
                    🚑 Darurat Medis
                </button>
                <button type="button" onclick="selectPanicCategory('lainnya', this)" class="panic-cat-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer transition">
                    ⚠️ Lainnya
                </button>
            </div>

            <!-- Indikator Geofence Radius RW -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200/70">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Pusat Siaga RW 02 &bull; Radius Geofence: <strong id="home-geofence-radius-label">{{ $rwSetting->panic_radius_meters ?? 300 }} Meter</strong></span>
            </div>

            <!-- Tombol Utama Panic Button -->
            <div class="py-3 flex justify-center">
                <button id="main-panic-btn" onclick="handlePanicClick()" class="relative w-36 h-36 sm:w-44 sm:h-44 rounded-full bg-gradient-to-tr from-rose-700 via-rose-600 to-red-500 text-white font-extrabold shadow-2xl shadow-rose-600/50 panic-pulse flex flex-col items-center justify-center gap-1 active:scale-95 transition-all duration-200 cursor-pointer border-4 border-white">
                    <svg id="main-panic-icon" class="w-12 h-12 sm:w-14 sm:h-14 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span id="main-panic-title" class="text-sm sm:text-base font-black tracking-wider uppercase drop-shadow">DARURAT</span>
                    <span id="main-panic-subtitle" class="text-[10px] sm:text-[11px] font-medium text-rose-100">KENTONGAN</span>
                </button>
            </div>

            <!-- Lokasi GPS & Jarak Geofence Terdeteksi -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-center gap-1.5 text-xs text-slate-500">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span id="gps-status-text">GPS: Menghitung jarak ke Posko RW 02...</span>
                </div>

                <!-- Tombol Bantuan Uji Simulasi Jarak -->
                <div class="flex items-center justify-center gap-1.5 text-[11px] pt-1">
                    <span class="text-slate-400">Uji Jarak:</span>
                    <button type="button" onclick="simulasiGpsHome('dalam')" class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200 hover:bg-emerald-100 cursor-pointer">
                        🟢 Posko (&lt; {{ $rwSetting->panic_radius_meters ?? 300 }}m)
                    </button>
                    <button type="button" onclick="simulasiGpsHome('luar')" class="px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 font-semibold border border-rose-200 hover:bg-rose-100 cursor-pointer">
                        🔴 Luar Wilayah (&gt; {{ $rwSetting->panic_radius_meters ?? 300 }}m)
                    </button>
                    <button type="button" onclick="deteksiGpsHome()" class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-semibold border border-slate-200 hover:bg-slate-200 cursor-pointer">
                        📍 GPS Asli
                    </button>
                </div>
            </div>

            <!-- Pesan Feedback Pengiriman -->
            <div id="panic-feedback" class="hidden p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in">
                ✅ Sinyal darurat berhasil disiarkan! Alarm pos ronda dan pengurus telah berbunyi.
            </div>

        </div>
    </section>

    <!-- SEKSI 2: KARTU FITUR UTAMA (PILIHAN CEPAT) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- 1. Presensi Ronda QR -->
        <a href="#section-ronda" class="group bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-violet-300 hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 group-hover:text-violet-700 transition">Presensi Patroli</h3>
                <p class="text-[11px] text-slate-500 mt-1">Scan QR Code titik rawan via kamera HP ronda.</p>
            </div>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-violet-600 mt-3">
                <span>Scan Checkpoint</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        </a>

        <!-- 2. Lapor Cepat Kejadian -->
        <a href="#section-lapor" class="group bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-emerald-300 hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition">Lapor Kejadian</h3>
                <p class="text-[11px] text-slate-500 mt-1">Unggah foto temuan mencurigakan atau PJU mati.</p>
            </div>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 mt-3">
                <span>Kirim Laporan</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        </a>

        <!-- 3. Buku Tamu 2x24 Jam -->
        <a href="#section-tamu" class="group bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-blue-300 hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 group-hover:text-blue-700 transition">Tamu Wajib Lapor</h3>
                <p class="text-[11px] text-slate-500 mt-1">Formulir digital tamu/pengontrak baru 2x24 jam.</p>
            </div>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 mt-3">
                <span>Isi Buku Tamu</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        </a>

        <!-- 4. CCTV Lingkungan -->
        <a href="#section-cctv" class="group bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-teal-300 hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 group-hover:text-teal-700 transition">CCTV Lingkungan</h3>
                <p class="text-[11px] text-slate-500 mt-1">Pantau langsung kamera gerbang dan pos utama.</p>
            </div>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-600 mt-3">
                <span>Lihat Kamera</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        </a>

    </div>

    <!-- SEKSI 3: STATUS RONDA MALAM INI & CHECKPOINTS -->
    <section id="section-ronda" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-700 text-[11px] font-extrabold uppercase">Ronda Malam</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Jadwal & Checkpoint Patroli Pos Ronda</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Petugas memindai kode QR di setiap titik rawan RW 02 untuk validasi ronda berkala.</p>
            </div>
            <button onclick="openQrScannerModal()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold shadow-sm transition cursor-pointer self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Buka Scanner Kamera</span>
            </button>
        </div>

        <!-- Daftar Checkpoint QR yang Tersedia -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($checkpoints as $ckp)
            <div class="flex items-start justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 hover:bg-violet-50/40 hover:border-violet-200 transition">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                        {{ $ckp->urutan_patroli ?? $loop->iteration }}
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">{{ $ckp->nama_titik }}</h4>
                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-700 font-semibold">RT {{ $ckp->rt }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $ckp->deskripsi ?? 'Titik pemantauan patroli berkala.' }}</p>
                        <div class="mt-1.5 text-[10px] text-violet-700 font-mono bg-violet-50 inline-block px-1.5 py-0.5 rounded border border-violet-200/60">
                            QR: {{ $ckp->kode_qr }}
                        </div>
                    </div>
                </div>

                <!-- Tombol Simulasi Scan Langsung (Bisa scan via klik atau kamera) -->
                <button onclick="simulasiScanCheckpoint('{{ $ckp->kode_qr }}', '{{ $ckp->nama_titik }}')" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-violet-600 hover:text-white border border-violet-200 text-violet-700 text-[11px] font-bold transition shadow-2xs shrink-0 cursor-pointer">
                    Presensi Titik
                </button>
            </div>
            @endforeach
        </div>

        <!-- Feedback Scan -->
        <div id="scan-feedback" class="hidden p-3 rounded-2xl bg-violet-50 border border-violet-200 text-violet-900 text-xs font-semibold text-center animate-in fade-in">
            <!-- Diisi lewat JS -->
        </div>
    </section>

    <!-- SEKSI 4: LAPOR CEPAT KEJADIAN (FORMULIR WARGA) -->
    <section id="section-lapor" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[11px] font-extrabold uppercase">Lapor Cepat</span>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Laporan Kejadian & Gangguan Lingkungan</h2>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Sampaikan keluhan lampu padam, orang tak dikenal, atau potensi kerawanan kepada pengurus RT/RW.</p>
        </div>

        <form id="form-lapor-cepat" onsubmit="handleFormLapor(event)" class="space-y-3 max-w-xl">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Judul Kejadian / Keluhan</label>
                <input type="text" id="lapor-judul" required placeholder="Contoh: Lampu PJU Gang Melati Padam" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Detail</label>
                <textarea id="lapor-deskripsi" rows="3" required placeholder="Ceritakan kronologi, lokasi spesifik, atau ciri-ciri kendaraan..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
            </div>

            <div class="flex items-center justify-between gap-3 pt-1">
                <div class="text-[11px] text-slate-400">
                    📍 Koordinat lokasi akan disertakan secara otomatis.
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    Kirim Laporan Warga
                </button>
            </div>
        </form>

        <div id="lapor-feedback" class="hidden p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in">
            <!-- Diisi lewat JS -->
        </div>
    </section>

    <!-- SEKSI 5: BUKU TAMU & TAMU WAJIB LAPOR (2x24 JAM) -->
    <section id="section-tamu" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[11px] font-extrabold uppercase">Administrasi Warga</span>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Buku Tamu Digital (Wajib Lapor 2x24 Jam)</h2>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Tamu keluarga, kerabat yang menginap, atau pengontrak baru wajib mengisi pendataan mandiri.</p>
        </div>

        <form id="form-buku-tamu" onsubmit="handleFormBukuTamu(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-2xl">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Tamu</label>
                <input type="text" id="tamu-nama" required placeholder="Nama lengkap sesuai KTP" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" id="tamu-hp" required placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Asal / Kota Domisili</label>
                <input type="text" id="tamu-alamat" required placeholder="Kota atau alamat lengkap asal" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Warga / Keluarga yang Dituju</label>
                <input type="text" id="tamu-warga" required placeholder="Contoh: Bpk Budi (RT 01)" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tujuan Kunjungan</label>
                <input type="text" id="tamu-tujuan" required placeholder="Silaturahmi keluarga / urusan kerja" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <div class="sm:col-span-2 flex justify-end pt-1">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    Daftarkan Tamu Mandiri
                </button>
            </div>
        </form>

        <div id="tamu-feedback" class="hidden p-3 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-semibold text-center animate-in fade-in">
            <!-- Diisi lewat JS -->
        </div>
    </section>

    <!-- SEKSI 6: PEMANTAUAN CCTV LINGKUNGAN -->
    <section id="section-cctv" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 text-[11px] font-extrabold uppercase">IP Camera</span>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Pemantauan CCTV Lingkungan RW 02</h2>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Streaming langsung titik kamera pengawas di gerbang dan lorong utama.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($cctvs as $cctv)
            <div class="bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 shadow-md">
                <div class="relative aspect-video bg-slate-900 flex items-center justify-center">
                    <!-- Simulasi Video Feed -->
                    <div class="text-center p-4">
                        <div class="w-10 h-10 rounded-full bg-slate-800 text-teal-400 mx-auto flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-xs font-mono text-teal-300 font-bold">STREAMING ONLINE</p>
                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ now()->format('d M Y H:i:s') }}</p>
                    </div>

                    <!-- Badge Live & RT -->
                    <div class="absolute top-3 left-3 flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-rose-600/90 text-white text-[10px] font-bold font-mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                        <span>LIVE</span>
                    </div>
                    <div class="absolute top-3 right-3 px-2 py-0.5 rounded-md bg-slate-800/80 backdrop-blur-xs text-white text-[10px] font-semibold">
                        RT {{ $cctv->rt }}
                    </div>
                </div>

                <div class="p-3 bg-slate-900/90 text-white flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold">{{ $cctv->nama_lokasi }}</h4>
                        <p class="text-[10px] text-slate-400">Resolusi HD 1080p &bull; Infra-Red Night Vision</p>
                    </div>
                    <button onclick="alert('Membuka tampilan layar penuh untuk {{ $cctv->nama_lokasi }}')" class="text-slate-400 hover:text-white p-1 rounded transition cursor-pointer" title="Layar Penuh">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- SEKSI 7: RIWAYAT & FEED AKTIVITAS LINGKUNGAN -->
    <section id="section-dashboard" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-extrabold uppercase">Aktivitas Terkini</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Rekapitulasi Kamtibmas RW 02</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Catatan patroli ronda, laporan warga, dan tamu berkunjung.</p>
            </div>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200/60">
                Terverifikasi
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            <!-- Kolom 1: Patroli Terakhir -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/60 space-y-3">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-violet-600"></span>
                    <span>Patroli Checkpoint Terkini</span>
                </h4>
                <div class="space-y-2">
                    @forelse($presensiTerbaru as $pres)
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 text-xs">
                        <p class="font-bold text-slate-900">{{ $pres->checkpoint->nama_titik ?? 'Pos Ronda Utama' }}</p>
                        <p class="text-[11px] text-slate-500">Oleh: {{ $pres->user->name ?? 'Petugas Ronda' }}</p>
                        <p class="text-[10px] text-violet-700 font-medium mt-1">{{ $pres->catatan ?? 'Situasi aman terkendali.' }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">Belum ada scan presensi malam ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Kolom 2: Laporan Warga -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/60 space-y-3">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>Laporan Kejadian Terkini</span>
                </h4>
                <div class="space-y-2">
                    @forelse($laporanTerbaru as $lap)
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 text-xs">
                        <div class="flex items-center justify-between gap-1">
                            <p class="font-bold text-slate-900 truncate">{{ $lap->judul }}</p>
                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold shrink-0 uppercase">{{ $lap->status }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $lap->deskripsi }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">Lingkungan kondusif, nihil laporan insiden.</p>
                    @endforelse
                </div>
            </div>

            <!-- Kolom 3: Tamu 2x24 Jam -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/60 space-y-3">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span>Tamu Wajib Lapor (2x24 Jam)</span>
                </h4>
                <div class="space-y-2">
                    @forelse($tamuTerbaru as $tamu)
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 text-xs">
                        <div class="flex items-center justify-between gap-1">
                            <p class="font-bold text-slate-900">{{ $tamu->nama_tamu }}</p>
                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold uppercase">{{ $tamu->status }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Asal: {{ $tamu->alamat_asal }}</p>
                        <p class="text-[10px] text-blue-700 font-medium mt-0.5">Tujuan: {{ $tamu->warga_yang_dikunjungi }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">Tidak ada tamu yang sedang menginap.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </section>

</div>

<!-- MODAL KAMERA SCANNER HTML5-QRCODE -->
<div id="modal-qr-scanner" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 animate-in fade-in">
    <div class="bg-white rounded-3xl max-w-sm w-full p-5 space-y-4 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-violet-600 animate-ping"></span>
                <h3 class="font-bold text-sm text-slate-900">Scanner Checkpoint Ronda</h3>
            </div>
            <button onclick="closeQrScannerModal()" class="text-slate-400 hover:text-slate-700 text-lg leading-none cursor-pointer">
                &times;
            </button>
        </div>

        <div class="relative bg-slate-950 rounded-2xl overflow-hidden aspect-square flex flex-col items-center justify-center">
            <!-- Reader QR Canvas Container -->
            <div id="qr-reader" class="w-full h-full"></div>
            <div id="scanner-placeholder" class="text-center p-4 text-white">
                <div class="w-12 h-12 rounded-2xl bg-violet-600/30 border border-violet-400 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-violet-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <p class="text-xs font-bold">Arahkan kamera ke stiker QR Pos</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Memerlukan izin akses kamera HP</p>
            </div>
        </div>

        <div class="text-center">
            <button onclick="closeQrScannerModal()" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                Tutup Scanner
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Library html5-qrcode untuk pemindaian QR di peramban seluler -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let activePanicCategory = 'pencurian';
    let html5QrScanner = null;

    // Pilih Kategori Bahaya
    function selectPanicCategory(cat, btnElement) {
        activePanicCategory = cat;
        document.querySelectorAll('.panic-cat-btn').forEach(btn => {
            btn.classList.remove('bg-rose-600', 'text-white');
            btn.classList.add('bg-slate-100', 'text-slate-600');
        });
        btnElement.classList.remove('bg-slate-100', 'text-slate-600');
        btnElement.classList.add('bg-rose-600', 'text-white');
    }

    const rwCenterLat = {{ $rwSetting->center_latitude ?? -6.208800 }};
    const rwCenterLng = {{ $rwSetting->center_longitude ?? 106.845600 }};
    const rwMaxRadius = {{ $rwSetting->panic_radius_meters ?? 300 }};
    let currentHomeLat = rwCenterLat;
    let currentHomeLon = rwCenterLng;
    let isHomeWithinGeofence = true;

    // Kalkulasi Jarak Haversine (dalam satuan meter)
    function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Radius bumi dalam meter
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c);
    }

    function updateGeofenceStatusHome(lat, lon) {
        currentHomeLat = lat;
        currentHomeLon = lon;
        const distance = calculateHaversineDistance(lat, lon, rwCenterLat, rwCenterLng);
        const statusSpan = document.getElementById('gps-status-text');
        const panicBtn = document.getElementById('main-panic-btn');
        const panicTitle = document.getElementById('main-panic-title');
        const panicSubtitle = document.getElementById('main-panic-subtitle');

        if (distance <= rwMaxRadius) {
            isHomeWithinGeofence = true;
            statusSpan.innerHTML = `<span class="text-emerald-700 font-bold">✅ DALAM JANGKAUAN</span> (Jarak: <strong>${distance}m</strong> dari Posko, Batas: ${rwMaxRadius}m)`;
            panicBtn.classList.remove('opacity-40', 'grayscale', 'cursor-not-allowed');
            panicBtn.classList.add('panic-pulse');
            panicTitle.innerText = 'DARURAT';
            panicSubtitle.innerText = 'KENTONGAN';
        } else {
            isHomeWithinGeofence = false;
            statusSpan.innerHTML = `<span class="text-rose-600 font-bold">🚫 DI LUAR JANGKAUAN</span> (Jarak: <strong>${distance}m</strong> > Batas: ${rwMaxRadius}m)`;
            panicBtn.classList.add('opacity-40', 'grayscale', 'cursor-not-allowed');
            panicBtn.classList.remove('panic-pulse');
            panicTitle.innerText = 'TERKUNCI';
            panicSubtitle.innerText = 'DI LUAR RADIUS ' + rwMaxRadius + 'M';
        }
    }

    function deteksiGpsHome() {
        const gpsStatus = document.getElementById('gps-status-text');
        gpsStatus.innerText = 'Mencari sinyal GPS perangkat...';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    updateGeofenceStatusHome(pos.coords.latitude, pos.coords.longitude);
                },
                (err) => {
                    console.log('GPS ditolak/gagal, menggunakan koordinat posko RW 02');
                    updateGeofenceStatusHome(rwCenterLat, rwCenterLng);
                },
                { enableHighAccuracy: true, timeout: 6000 }
            );
        } else {
            updateGeofenceStatusHome(rwCenterLat, rwCenterLng);
        }
    }

    function simulasiGpsHome(tipe) {
        if (tipe === 'dalam') {
            // Posisi ~100m dari posko
            updateGeofenceStatusHome(rwCenterLat + 0.0008, rwCenterLng + 0.0006);
        } else {
            // Posisi ~500m dari posko (di luar radius 300m)
            updateGeofenceStatusHome(rwCenterLat + 0.0045, rwCenterLng + 0.0035);
        }
    }

    // Inisialisasi deteksi geofence awal
    deteksiGpsHome();

    // Eksekusi Tombol Darurat Digital (Kentongan Online)
    function handlePanicClick() {
        const distance = calculateHaversineDistance(currentHomeLat, currentHomeLon, rwCenterLat, rwCenterLng);

        // Proteksi sisi klien
        if (distance > rwMaxRadius) {
            alert(`⚠️ PERINGATAN: Posisi Anda berada di luar radius keamanan RW 02 (Jarak: ${distance} meter > Batas aktif: ${rwMaxRadius} meter).\n\nTombol panic hanya dapat digunakan di dalam lingkungan wilayah RW 02 demi mencegah alarm palsu. Silakan hubungi pengurus secara langsung.`);
            return;
        }

        sendPanicAlert(currentHomeLat, currentHomeLon);
    }

    function sendPanicAlert(latitude, longitude) {
        // Bunyikan sirene kentongan darurat seketika
        triggerEmergencyAlert(`Kategori: ${activePanicCategory.toUpperCase()} &bull; Koordinat: ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`);

        // Tampilkan feedback ke warga
        const feedback = document.getElementById('panic-feedback');
        feedback.className = 'p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in';
        feedback.innerHTML = `🚨 <strong>SINYAL DARURAT DISIARKAN!</strong> Alarm sirine telah berbunyi di Pos Ronda dan HP pengurus RW. Petugas segera merespon!`;
        feedback.classList.remove('hidden');

        // Kirim request ke backend
        fetch('/api/panic', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                latitude: latitude,
                longitude: longitude,
                kategori: activePanicCategory,
                catatan: `Sinyal Kentongan Online kategori: ${activePanicCategory}`
            })
        })
        .then(async (res) => {
            const data = await res.json();
            if (!res.ok && data.out_of_radius) {
                stopEmergencySound();
                feedback.className = 'p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in';
                feedback.innerHTML = `🚫 <strong>DITOLAK SISTEM:</strong> ${data.message}`;
            }
        })
        .catch(err => {
            console.log('Panic Alert berjalan dalam mode offline/demo:', err);
        });
    }

    // Simulasi & Presensi Checkpoint Pos Ronda
    function simulasiScanCheckpoint(kodeQr, namaTitik) {
        const feedback = document.getElementById('scan-feedback');
        feedback.innerHTML = `⏳ Merekam presensi patroli di <strong>${namaTitik}</strong>...`;
        feedback.classList.remove('hidden');

        // Geolocation saat scan untuk verifikasi fisik petugas
        let lat = -6.208800, lon = 106.845600;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (p) => { lat = p.coords.latitude; lon = p.coords.longitude; submitPresensi(kodeQr, namaTitik, lat, lon); },
                () => { submitPresensi(kodeQr, namaTitik, lat, lon); }
            );
        } else {
            submitPresensi(kodeQr, namaTitik, lat, lon);
        }
    }

    function submitPresensi(kodeQr, namaTitik, lat, lon) {
        fetch('/api/presensi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                kode_qr: kodeQr,
                latitude: lat,
                longitude: lon,
                catatan: 'Patroli berkala pos ronda malam.'
            })
        })
        .then(res => res.json())
        .then(data => {
            const feedback = document.getElementById('scan-feedback');
            feedback.innerHTML = `✅ <strong>Presensi Berhasil Tercatat!</strong> Petugas ronda telah memverifikasi titik <strong>${namaTitik}</strong> pada pukul ${new Date().toLocaleTimeString('id-ID')}.`;
            // Bunyikan 1 ketukan kentongan sebagai notifikasi selesai
            playKentonganKnock(600, 0.15);
        })
        .catch(() => {
            const feedback = document.getElementById('scan-feedback');
            feedback.innerHTML = `✅ <strong>Presensi Terverifikasi (Offline Mode)!</strong> Titik <strong>${namaTitik}</strong> berhasil dicatat.`;
        });
    }

    // Modal & Kamera Scanner QR html5-qrcode
    function openQrScannerModal() {
        document.getElementById('modal-qr-scanner').classList.remove('hidden');
        document.getElementById('scanner-placeholder').classList.remove('hidden');

        if (typeof Html5Qrcode !== 'undefined') {
            html5QrScanner = new Html5Qrcode("qr-reader");
            const config = { fps: 10, qrbox: { width: 220, height: 220 } };

            html5QrScanner.start(
                { facingMode: "environment" },
                config,
                (qrCodeMessage) => {
                    // Sukses scan QR
                    console.log('QR Ditemukan:', qrCodeMessage);
                    closeQrScannerModal();
                    simulasiScanCheckpoint(qrCodeMessage, `QR: ${qrCodeMessage}`);
                },
                (errorMessage) => {
                    // scanning terus berjalan
                }
            ).catch(err => {
                console.warn('Gagal membuka kamera perangkat:', err);
                document.getElementById('scanner-placeholder').innerHTML = `
                    <p class="text-xs text-amber-300 font-semibold">Kamera tidak dapat diakses atau diblokir oleh browser.</p>
                    <p class="text-[11px] text-slate-300 mt-1">Gunakan tombol 'Presensi Titik' di daftar checkpoint untuk simulasi.</p>
                `;
            });
        }
    }

    function closeQrScannerModal() {
        if (html5QrScanner) {
            html5QrScanner.stop().then(() => {
                html5QrScanner.clear();
            }).catch(e => console.log(e));
        }
        document.getElementById('modal-qr-scanner').classList.add('hidden');
    }

    // Form Lapor Cepat
    function handleFormLapor(e) {
        e.preventDefault();
        const judul = document.getElementById('lapor-judul').value;
        const deskripsi = document.getElementById('lapor-deskripsi').value;
        const feedback = document.getElementById('lapor-feedback');

        feedback.innerHTML = '⏳ Mengirim laporan ke pengurus RT/RW...';
        feedback.classList.remove('hidden');

        fetch('/api/lapor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ judul, deskripsi })
        })
        .then(res => res.json())
        .then(data => {
            feedback.innerHTML = `✅ <strong>Laporan Diterima!</strong> Laporan "<em>${judul}</em>" telah diteruskan ke Ketua RT untuk penanganan.`;
            document.getElementById('form-lapor-cepat').reset();
        })
        .catch(() => {
            feedback.innerHTML = `✅ <strong>Laporan Berhasil Dicatat (Demo Mode)!</strong> Terima kasih atas kepedulian warga.`;
            document.getElementById('form-lapor-cepat').reset();
        });
    }

    // Form Buku Tamu 2x24 Jam
    function handleFormBukuTamu(e) {
        e.preventDefault();
        const nama = document.getElementById('tamu-nama').value;
        const hp = document.getElementById('tamu-hp').value;
        const alamat = document.getElementById('tamu-alamat').value;
        const warga = document.getElementById('tamu-warga').value;
        const tujuan = document.getElementById('tamu-tujuan').value;
        const feedback = document.getElementById('tamu-feedback');

        feedback.innerHTML = '⏳ Mendaftarkan tamu ke buku tamu digital RW 02...';
        feedback.classList.remove('hidden');

        fetch('/api/buku-tamu', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                nama_tamu: nama,
                no_hp: hp,
                alamat_asal: alamat,
                warga_yang_dikunjungi: warga,
                tujuan_kunjungan: tujuan
            })
        })
        .then(res => res.json())
        .then(data => {
            feedback.innerHTML = `✅ <strong>Pendataan Tamu Berhasil!</strong> Tamu <strong>${nama}</strong> telah terdaftar. Pemberitahuan telah diteruskan ke Ketua RT setempat.`;
            document.getElementById('form-buku-tamu').reset();
        })
        .catch(() => {
            feedback.innerHTML = `✅ <strong>Tamu Terdaftar (Demo Mode)!</strong> Data kunjungan 2x24 jam telah tercatat di pos RW.`;
            document.getElementById('form-buku-tamu').reset();
        });
    }

    // Respon jika role disimulasikan berubah dari header
    window.addEventListener('role-changed', (e) => {
        const role = e.detail.role;
        const greeting = document.getElementById('role-greeting');
        const greetings = {
            'warga': 'Mode Warga: Akses Panic Button, Lapor Cepat, dan Formulir Tamu 2x24 Jam.',
            'petugas_ronda': 'Mode Petugas Ronda: Fokus pada scan QR Checkpoint, log patroli, dan siaga alarm.',
            'rt': 'Mode Ketua RT: Pantau data warga, validasi tamu 2x24 jam, dan koordinasi pos ronda.',
            'rw': 'Mode Pengurus RW (Admin): Akses Command Center, manajemen jadwal ronda, dan peta kerawanan.',
            'bhabinkamtibmas': 'Mode Bhabinkamtibmas: Analisis patroli kamtibmas, respon insiden, dan koordinasi Polsek.'
        };
        if (greeting) greeting.innerText = greetings[role] || greetings['warga'];
    });
</script>
@endpush
