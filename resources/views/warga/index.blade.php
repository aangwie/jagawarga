@extends('layouts.app')

@section('title', 'Portal Warga - JagaWarga RW 02')

@section('content')
<div class="space-y-6">

    <!-- Header Portal Warga -->
    <div class="bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-800 rounded-3xl p-5 sm:p-6 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-36 h-36 rounded-full bg-white/10 blur-xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl shadow-inner">
                    🏠
                </div>
                <div>
                    <span class="inline-block px-2 py-0.5 rounded-md bg-white/20 text-emerald-100 text-[10px] font-extrabold uppercase tracking-wider mb-1">
                        Sisi Warga Lingkungan
                    </span>
                    <h1 class="text-lg sm:text-xl font-black tracking-tight">Portal Pelayanan Keamanan Warga</h1>
                    <p class="text-xs text-emerald-100 mt-0.5">Akses cepat tombol darurat, pengaduan lingkungan, dan wajib lapor tamu.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="#panic-section" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span>Kentongan Online</span>
                </a>
            </div>
        </div>
    </div>

    <!-- SEKSI 1: KENTONGAN ONLINE DARURAT -->
    <section id="panic-section" class="bg-white rounded-3xl p-6 border-2 border-rose-150 shadow-sm relative overflow-hidden">
        <div class="max-w-md mx-auto text-center space-y-4">
            
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold border border-rose-100">
                <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                <span>TOMBOL DARURAT DIGITAL</span>
            </div>

            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kentongan Online RW 02</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Cukup satu sentuhan untuk mengirim alarm bahaya ke ponsel petugas ronda poskamling, pengurus RW, dan Bhabinkamtibmas.
                </p>
            </div>

            <!-- Kategori Bahaya Selector -->
            <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                <button type="button" onclick="selectPanicCat('pencurian', this)" class="panic-chip px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-600 text-white shadow-xs transition cursor-pointer">
                    🚨 Maling / Curanmor
                </button>
                <button type="button" onclick="selectPanicCat('kebakaran', this)" class="panic-chip px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition cursor-pointer">
                    🔥 Kebakaran
                </button>
                <button type="button" onclick="selectPanicCat('medis', this)" class="panic-chip px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition cursor-pointer">
                    🚑 Darurat Medis
                </button>
                <button type="button" onclick="selectPanicCat('lainnya', this)" class="panic-chip px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition cursor-pointer">
                    ⚠️ Bahaya Lainnya
                </button>
            </div>

            <!-- Penjelasan Ciri Khas Bunyi Suara Kentongan Kategori Ini -->
            <div id="warga-sound-hint" class="text-[11px] px-3.5 py-2 rounded-2xl bg-slate-50 text-slate-700 border border-slate-200/80 flex items-center justify-between gap-2 max-w-sm mx-auto shadow-xs">
                <div class="flex items-center gap-2 text-left">
                    <span id="w-sound-hint-icon" class="text-base">🚨</span>
                    <div>
                        <span class="font-extrabold text-slate-800" id="w-sound-hint-title">Maling / Curanmor</span>
                        <p class="text-[10px] text-slate-500 leading-tight" id="w-sound-hint-desc">Ketukan bertubi-tubi sangat cepat (Doro Muluk) & sirene maling</p>
                    </div>
                </div>
                <button type="button" onclick="previewWargaCategorySound()" title="Dengarkan Contoh Bunyi" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-[10px] cursor-pointer transition shrink-0 border border-rose-200 flex items-center gap-1">
                    <span>🔊</span> <span>Tes Bunyi</span>
                </button>
            </div>

            <!-- Indikator Geofence Radius RW -->
            <!-- PANEL 2 KONDISI KESELAMATAN AKTIVASI KENTONGAN ONLINE -->
            <div class="bg-slate-50/90 rounded-2xl p-3 sm:p-4 border border-slate-200/90 text-left space-y-2.5 max-w-md mx-auto shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                    <span class="text-[11px] font-extrabold uppercase text-slate-700 tracking-wider flex items-center gap-1.5">
                        <span>🛡️</span> Syarat Aktivasi Kentongan (2 Kondisi Wajib)
                    </span>
                    <span id="warga-overall-badge" class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200">
                        @if(Auth::check() && Auth::user()->isNikVerified())
                            Menunggu Lokasi GPS
                        @else
                            Wajib Terverifikasi NIK
                        @endif
                    </span>
                </div>

                <!-- Kondisi 1: Warga Terverifikasi NIK -->
                <div class="flex items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-2">
                        @if(Auth::check() && Auth::user()->isNikVerified())
                            <span class="w-5 h-5 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-black text-[10px] shrink-0">✓</span>
                            <div>
                                <span class="font-bold text-slate-800 block text-[11px]">1. Verifikasi NIK Warga</span>
                                <span class="text-[10px] text-emerald-700 block font-medium">{{ Auth::user()->name }} (NIK: <strong class="font-mono">{{ Auth::user()->nik }}</strong>)</span>
                            </div>
                        @elseif(Auth::check())
                            <span class="w-5 h-5 rounded-lg bg-amber-500 text-white flex items-center justify-center font-black text-[10px] shrink-0">⚠️</span>
                            <div>
                                <span class="font-bold text-slate-800 block text-[11px]">1. Verifikasi NIK Warga</span>
                                <span class="text-[10px] text-amber-700 block font-medium">NIK belum diverifikasi pengurus RW</span>
                            </div>
                        @else
                            <span class="w-5 h-5 rounded-lg bg-rose-500 text-white flex items-center justify-center font-black text-[10px] shrink-0">🔒</span>
                            <div>
                                <span class="font-bold text-slate-800 block text-[11px]">1. Verifikasi NIK Warga</span>
                                <span class="text-[10px] text-rose-700 block font-medium">Wajib masuk akun warga ber-NIK terverifikasi</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        @if(Auth::check() && Auth::user()->isNikVerified())
                            <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">TERPENUHI</span>
                        @elseif(Auth::check())
                            <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 shrink-0">BELUM DIVERIFIKASI</span>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-[10px] shadow-2xs transition shrink-0">
                                <span>🔑</span> <span>Masuk NIK</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Kondisi 2: Radius Geofence Lingkungan RW -->
                <div class="flex items-center justify-between gap-2 text-xs pt-1 border-t border-slate-200/60">
                    <div class="flex items-center gap-2">
                        <span id="warga-cond2-icon" class="w-5 h-5 rounded-lg bg-amber-500 text-white flex items-center justify-center font-black text-[10px] shrink-0">📍</span>
                        <div>
                            <span class="font-bold text-slate-800 block text-[11px]">2. Radius Geofence RW 02</span>
                            <span id="warga-cond2-text" class="text-[10px] text-slate-500 block font-medium">Batas radius: &le; {{ $rwSetting->panic_radius_meters ?? 300 }} Meter</span>
                        </div>
                    </div>
                    <span id="warga-cond2-badge" class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 shrink-0">
                        MENUNGGU GPS
                    </span>
                </div>
            </div>

            <!-- Tombol Utama Panic Button (Hanya aktif jika 2 KONDISI TERPENUHI) -->
            <div class="py-3 flex justify-center">
                <button id="warga-panic-btn" onclick="triggerWargaPanic()" class="relative w-40 h-40 sm:w-48 sm:h-48 rounded-full bg-gradient-to-tr from-rose-700 via-rose-600 to-red-500 text-white font-extrabold shadow-2xl shadow-rose-600/50 flex flex-col items-center justify-center gap-1 active:scale-95 transition-all duration-200 cursor-not-allowed opacity-40 grayscale border-4 border-white">
                    <svg id="panic-icon" class="w-14 h-14 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span id="panic-btn-title" class="text-sm sm:text-base font-black tracking-wider uppercase drop-shadow text-center px-2">
                        @if(!Auth::check())
                            NIK BELUM VERIFIKASI
                        @elseif(!Auth::user()->isNikVerified())
                            NIK BELUM DISETUJUI
                        @else
                            LOKASI DIBUTUHKAN
                        @endif
                    </span>
                    <span id="panic-btn-subtitle" class="text-[10px] sm:text-[11px] font-medium text-rose-100 uppercase tracking-tight">
                        @if(!Auth::check())
                            MASUK DENGAN NIK
                        @elseif(!Auth::user()->isNikVerified())
                            HUBUNGI KETUA RT/RW
                        @else
                            IZINKAN AKSES LOKASI
                        @endif
                    </span>
                </button>
            </div>

            <!-- Status Jarak & Koordinat GPS Terkini -->
            <div class="space-y-2">
                <div class="flex items-center justify-center gap-1.5 text-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span id="warga-gps-status" class="text-slate-600 font-medium"><span class="text-amber-600 font-bold">⚠️ Menunggu Izin Lokasi:</span> Tombol kentongan dinonaktifkan sampai lokasi diperoleh.</span>
                </div>

                <!-- Tombol Minta Izin Lokasi Peramban -->
                <div class="flex justify-center">
                    <button type="button" id="btn-minta-lokasi-warga" onclick="mintaIzinGpsWarga()" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition cursor-pointer flex items-center gap-1.5">
                        <span>📍</span>
                        <span>Aktifkan / Izinkan Lokasi GPS</span>
                    </button>
                </div>

                <!-- Tombol Bantuan Uji Simulasi Jarak (Dalam vs Luar Radius) -->
                <div class="flex items-center justify-center gap-1.5 text-[11px] pt-1 border-t border-slate-100">
                    <span class="text-slate-400">Uji Jarak:</span>
                    <button type="button" onclick="simulasiLokasiGps('dalam')" class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200 hover:bg-emerald-100 cursor-pointer">
                        🟢 Posko (&lt; {{ $rwSetting->panic_radius_meters ?? 300 }}m)
                    </button>
                    <button type="button" onclick="simulasiLokasiGps('luar')" class="px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 font-semibold border border-rose-200 hover:bg-rose-100 cursor-pointer">
                        🔴 Luar Wilayah (&gt; {{ $rwSetting->panic_radius_meters ?? 300 }}m)
                    </button>
                    <button type="button" onclick="mintaIzinGpsWarga()" class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-semibold border border-slate-200 hover:bg-slate-200 cursor-pointer">
                        📍 GPS Asli
                    </button>
                </div>
            </div>

            <div id="warga-panic-feedback" class="hidden p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in">
                <!-- Diisi via JS -->
            </div>

        </div>
    </section>

    <!-- GRID 2 KOLOM: LAPOR CEPAT & BUKU TAMU -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- FORMULIR LAPOR CEPAT KEJADIAN -->
        <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">Lapor Cepat</span>
                    <h3 class="text-base font-bold text-slate-900">Pengaduan & Laporan Kejadian</h3>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Unggah temuan mencurigakan, PJU padam, atau tindak kriminal.</p>
            </div>

            <form id="form-lapor-warga" onsubmit="submitLaporWarga(event)" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Laporan</label>
                    <input type="text" id="lapor-w-judul" required placeholder="Contoh: Pagar Rumah Blok A Dirusak Orang Asing" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Kronologi Singkat</label>
                    <textarea id="lapor-w-deskripsi" rows="3" required placeholder="Jelaskan ciri-ciri pelaku, plat nomor, atau lokasi spesifik..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Lampiran Foto (Opsional)</label>
                    <input type="file" id="lapor-w-foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                </div>

                <div class="pt-1 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                        Kirim Laporan ke Pengurus RT
                    </button>
                </div>
            </form>

            <div id="lapor-w-feedback" class="hidden p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in"></div>
        </section>

        <!-- FORMULIR BUKU TAMU WAJIB LAPOR (2x24 JAM) -->
        <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase">Wajib Lapor</span>
                    <h3 class="text-base font-bold text-slate-900">Buku Tamu Digital (2x24 Jam)</h3>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Pendataan mandiri sebelum melapor fisik ke Ketua RT setempat.</p>
            </div>

            <form id="form-tamu-warga" onsubmit="submitTamuWarga(event)" class="space-y-3">
                <!-- Pilihan Kewarganegaraan Tamu -->
                <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Kewarganegaraan Tamu</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-blue-500 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 has-[:checked]:text-blue-900 font-bold text-xs text-slate-700 shadow-2xs">
                            <input type="radio" name="tamu_w_kewarganegaraan" value="WNI" checked onchange="toggleKewarganegaraanTamuWarga('WNI')" class="accent-blue-600">
                            <span>🇮🇩 WNI (Indonesia)</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-blue-500 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 has-[:checked]:text-blue-900 font-bold text-xs text-slate-700 shadow-2xs">
                            <input type="radio" name="tamu_w_kewarganegaraan" value="WNA" onchange="toggleKewarganegaraanTamuWarga('WNA')" class="accent-blue-600">
                            <span>🌐 WNA (Asing)</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tamu</label>
                        <input type="text" id="tamu-w-nama" required placeholder="Nama lengkap sesuai dokumen identitas" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Identitas Dinamis: NIK untuk WNI -->
                    <div id="wrap-tamu-w-nik">
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            NIK (Nomor Induk Kependudukan) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="tamu-w-nik" required maxlength="16" pattern="[0-9]{16}" placeholder="16 Digit NIK sesuai e-KTP" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 font-mono">
                        <span class="text-[9px] text-slate-400 mt-0.5 block">Wajib 16 digit angka sesuai KTP tamu WNI</span>
                    </div>

                    <!-- Identitas Dinamis: Paspor untuk WNA -->
                    <div id="wrap-tamu-w-paspor" class="hidden">
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Nomor Paspor / Dokumen Imigrasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="tamu-w-paspor" maxlength="50" placeholder="Contoh: A12345678 / Paspor Resmi" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 font-mono uppercase">
                        <span class="text-[9px] text-slate-400 mt-0.5 block">Wajib nomor paspor bagi tamu WNA</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
                        <input type="tel" id="tamu-w-hp" required placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Asal / Negara Asal</label>
                        <input type="text" id="tamu-w-alamat" required placeholder="Kota domisili atau negara asal" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Warga yang Dikunjungi</label>
                        <input type="text" id="tamu-w-tujuan" required placeholder="Contoh: Budi Santoso (RT 01)" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tujuan Kunjungan</label>
                        <input type="text" id="tamu-w-keperluan" required placeholder="Silaturahmi keluarga / urusan kerja" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="pt-1 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                        Daftarkan Tamu 2x24 Jam
                    </button>
                </div>
            </form>

            <div id="tamu-w-feedback" class="hidden p-3 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-semibold text-center animate-in fade-in"></div>
        </section>

    </div>

    <!-- SEKSI 3: RIWAYAT PELAPORAN & TAMU TERKINI -->
    <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Riwayat Laporan & Tamu Warga RW 02</h3>
                <p class="text-xs text-slate-500">Transparansi penanganan keluhan dan pendataan warga lingkungan.</p>
            </div>
            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                RW 02 Transparan
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Daftar Laporan -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Laporan Warga Terkini</h4>
                @forelse($laporanList as $lap)
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60 text-xs space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-bold text-slate-900">{{ $lap->judul }}</p>
                        <span class="text-[9px] px-2 py-0.5 rounded-full font-bold uppercase {{ $lap->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $lap->status }}
                        </span>
                    </div>
                    <p class="text-slate-600 line-clamp-2">{{ $lap->deskripsi }}</p>
                    <p class="text-[10px] text-slate-400">Oleh: {{ $lap->user->name ?? 'Warga' }} &bull; {{ $lap->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <p class="text-xs text-slate-400 p-3 bg-slate-50 rounded-xl">Belum ada laporan warga terbaru.</p>
                @endforelse
            </div>

            <!-- Daftar Tamu -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tamu Wajib Lapor (2x24 Jam)</h4>
                @forelse($tamuList as $tamu)
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60 text-xs space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-bold text-slate-900">{{ $tamu->nama_tamu }}</p>
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold uppercase">
                            {{ $tamu->status }}
                        </span>
                    </div>
                    <div>
                        @if(($tamu->kewarganegaraan ?? 'WNI') === 'WNA')
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">🌐 WNA • Paspor: {{ $tamu->nomor_paspor ?? '-' }}</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-bold">🇮🇩 WNI • NIK: {{ $tamu->nik ?? '-' }}</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500">Asal: {{ $tamu->alamat_asal }}</p>
                    <p class="text-[10px] text-blue-700 font-medium">Tujuan: {{ $tamu->warga_yang_dikunjungi }} &bull; Masuk: {{ \Carbon\Carbon::parse($tamu->tanggal_tiba)->format('d M Y H:i') }}</p>
                </div>
                @empty
                <p class="text-xs text-slate-400 p-3 bg-slate-50 rounded-xl">Tidak ada tamu yang sedang menginap.</p>
                @endforelse
            </div>

        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
    let selectedPanicCategory = 'pencurian';
    
    // Konfigurasi Geofencing RW dari Database
    const rwCenterLat = {{ $rwSetting->center_latitude ?? -6.208800 }};
    const rwCenterLng = {{ $rwSetting->center_longitude ?? 106.845600 }};
    const rwMaxRadius = {{ $rwSetting->panic_radius_meters ?? 300 }};

    let currentLat = null;
    let currentLon = null;
    let locationAcquiredWarga = false;
    let isWithinGeofence = false;

    // Kalkulator Rumus Haversine (menghitung jarak dua titik GPS dalam meter)
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

    const isWargaNikVerified = {{ (Auth::check() && Auth::user()->isNikVerified()) ? 'true' : 'false' }};
    const isWargaLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    const currentWargaName = "{{ Auth::check() ? addslashes(Auth::user()->name) : '' }}";

    function evaluateWargaPanicButton() {
        const panicBtn = document.getElementById('warga-panic-btn');
        const panicTitle = document.getElementById('panic-btn-title');
        const panicSubtitle = document.getElementById('panic-btn-subtitle');
        const overallBadge = document.getElementById('warga-overall-badge');
        const cond2Icon = document.getElementById('warga-cond2-icon');
        const cond2Badge = document.getElementById('warga-cond2-badge');
        const cond2Text = document.getElementById('warga-cond2-text');

        // KONDISI 1 GAGAL
        if (!isWargaNikVerified) {
            if (panicBtn) {
                panicBtn.classList.add('opacity-40', 'grayscale', 'cursor-not-allowed');
                panicBtn.classList.remove('panic-pulse');
            }
            if (!isWargaLoggedIn) {
                if (panicTitle) panicTitle.innerText = 'NIK BELUM VERIFIKASI';
                if (panicSubtitle) panicSubtitle.innerText = 'MASUK DENGAN NIK';
                if (overallBadge) {
                    overallBadge.className = 'text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 border border-rose-200';
                    overallBadge.innerText = 'Wajib Terverifikasi NIK';
                }
            } else {
                if (panicTitle) panicTitle.innerText = 'NIK BELUM DISETUJUI';
                if (panicSubtitle) panicSubtitle.innerText = 'MENUNGGU VERIFIKASI RT';
                if (overallBadge) {
                    overallBadge.className = 'text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200';
                    overallBadge.innerText = 'NIK Belum Terverifikasi';
                }
            }
            return;
        }

        // KONDISI 1 LOLOS, PERIKSA KONDISI 2
        if (!locationAcquiredWarga || currentLat === null || currentLon === null) {
            if (panicBtn) {
                panicBtn.classList.add('opacity-40', 'grayscale', 'cursor-not-allowed');
                panicBtn.classList.remove('panic-pulse');
            }
            if (panicTitle) panicTitle.innerText = 'LOKASI DIBUTUHKAN';
            if (panicSubtitle) panicSubtitle.innerText = 'IZINKAN AKSES LOKASI';
            if (overallBadge) {
                overallBadge.className = 'text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200';
                overallBadge.innerText = 'Menunggu Lokasi GPS';
            }
            return;
        }

        const distance = calculateHaversineDistance(currentLat, currentLon, rwCenterLat, rwCenterLng);

        if (distance <= rwMaxRadius) {
            isWithinGeofence = true;
            if (panicBtn) {
                panicBtn.classList.remove('opacity-40', 'grayscale', 'cursor-not-allowed');
                panicBtn.classList.add('panic-pulse');
            }
            if (panicTitle) panicTitle.innerText = 'DARURAT';
            if (panicSubtitle) panicSubtitle.innerText = 'BUNYIKAN KENTONGAN';

            if (cond2Icon) {
                cond2Icon.className = 'w-5 h-5 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-black text-[10px] shrink-0';
                cond2Icon.innerText = '✓';
            }
            if (cond2Badge) {
                cond2Badge.className = 'text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0';
                cond2Badge.innerText = `TERPENUHI (${distance}M)`;
            }
            if (cond2Text) {
                cond2Text.innerHTML = `✅ Dalam jangkauan posko (Jarak: <strong>${distance}m</strong> &le; Batas: ${rwMaxRadius}m)`;
            }
            if (overallBadge) {
                overallBadge.className = 'text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-200';
                overallBadge.innerText = '✅ 2 Syarat Aktif';
            }
        } else {
            isWithinGeofence = false;
            if (panicBtn) {
                panicBtn.classList.add('opacity-40', 'grayscale', 'cursor-not-allowed');
                panicBtn.classList.remove('panic-pulse');
            }
            if (panicTitle) panicTitle.innerText = 'DI LUAR RADIUS';
            if (panicSubtitle) panicSubtitle.innerText = `TERKUNCI (> ${rwMaxRadius}M)`;

            if (cond2Icon) {
                cond2Icon.className = 'w-5 h-5 rounded-lg bg-rose-500 text-white flex items-center justify-center font-black text-[10px] shrink-0';
                cond2Icon.innerText = '✕';
            }
            if (cond2Badge) {
                cond2Badge.className = 'text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200 shrink-0';
                cond2Badge.innerText = `DI LUAR RADIUS (${distance}M)`;
            }
            if (cond2Text) {
                cond2Text.innerHTML = `<span class="text-rose-600 font-bold">Di luar jangkauan</span> (Jarak: <strong>${distance}m</strong> &gt; Batas: ${rwMaxRadius}m)`;
            }
            if (overallBadge) {
                overallBadge.className = 'text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 border border-rose-200';
                overallBadge.innerText = '🚫 Di Luar Radius RW';
            }
        }
    }

    // Set status tombol Nonaktif / Terkunci jika lokasi belum diizinkan atau belum diperoleh
    function setGpsDisabledStateWarga(reason = 'pending', customMsg = '') {
        locationAcquiredWarga = false;
        isWithinGeofence = false;
        currentLat = null;
        currentLon = null;

        const statusSpan = document.getElementById('warga-gps-status');
        const btnMinta = document.getElementById('btn-minta-lokasi-warga');

        if (btnMinta) btnMinta.classList.remove('hidden');

        if (reason === 'denied') {
            if (statusSpan) statusSpan.innerHTML = `<span class="text-rose-600 font-bold">🚫 IZIN LOKASI DITOLAK:</span> Buka izin lokasi di peramban agar koordinat dapat diverifikasi.`;
        } else if (reason === 'unsupported') {
            if (statusSpan) statusSpan.innerHTML = `<span class="text-rose-600 font-bold">⚠️ GPS Tidak Didukung:</span> Peramban Anda tidak mendukung sensor lokasi.`;
        } else {
            if (statusSpan) statusSpan.innerHTML = customMsg || `<span class="text-amber-600 font-bold">⚠️ Menunggu Izin Lokasi:</span> Tombol kentongan dinonaktifkan sampai lokasi diperoleh & diverifikasi.`;
        }

        evaluateWargaPanicButton();
    }

    function updateGeofenceStatus(lat, lon) {
        currentLat = lat;
        currentLon = lon;
        locationAcquiredWarga = true;

        const distance = calculateHaversineDistance(lat, lon, rwCenterLat, rwCenterLng);
        const statusSpan = document.getElementById('warga-gps-status');
        const btnMinta = document.getElementById('btn-minta-lokasi-warga');

        if (btnMinta) btnMinta.classList.add('hidden');

        if (distance <= rwMaxRadius) {
            statusSpan.innerHTML = `<span class="text-emerald-700 font-bold">✅ KOORDINAT TERVERIFIKASI</span> (Jarak: <strong>${distance}m</strong> dari Posko, Batas: ${rwMaxRadius}m)`;
        } else {
            statusSpan.innerHTML = `<span class="text-rose-600 font-bold">🚫 KOORDINAT DI LUAR WILAYAH</span> (Jarak: <strong>${distance}m</strong> > Batas: ${rwMaxRadius}m)`;
        }

        evaluateWargaPanicButton();
    }

    // Inisialisasi Deteksi GPS saat halaman dimuat
    function mintaIzinGpsWarga() {
        const gpsStatus = document.getElementById('warga-gps-status');
        if (gpsStatus) gpsStatus.innerHTML = '⏳ <em>Mendeteksi sinyal koordinat GPS perangkat...</em>';

        if (!navigator.geolocation) {
            setGpsDisabledStateWarga('unsupported');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                updateGeofenceStatus(pos.coords.latitude, pos.coords.longitude);
            },
            (err) => {
                console.warn('GPS Warga error/denied:', err);
                if (err.code === 1) { // PERMISSION_DENIED
                    setGpsDisabledStateWarga('denied');
                } else {
                    setGpsDisabledStateWarga('error', `<span class="text-rose-600 font-bold">⚠️ Gagal Membaca GPS:</span> ${err.message}. Tombol dinonaktifkan.`);
                }
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    function deteksiGpsAsli() {
        mintaIzinGpsWarga();
    }

    // Simulasi uji coba jarak
    function simulasiLokasiGps(tipe) {
        if (tipe === 'dalam') {
            // Posisi 100 meter dari pusat posko
            updateGeofenceStatus(rwCenterLat + 0.0008, rwCenterLng + 0.0006);
        } else {
            // Posisi 500 meter dari pusat posko (di luar radius 300m)
            updateGeofenceStatus(rwCenterLat + 0.0045, rwCenterLng + 0.0035);
        }
    }

    // Default tombol NONAKTIF sampai lokasi diperoleh
    setGpsDisabledStateWarga('pending');
    mintaIzinGpsWarga();

    const wargaCategoryInfo = {
        'pencurian': {
            icon: '🚨',
            title: 'Maling / Curanmor',
            desc: 'Ketukan bertubi-tubi sangat cepat (Doro Muluk) & sirene maling',
            btnBg: 'bg-rose-600'
        },
        'kebakaran': {
            icon: '🔥',
            title: 'Bahaya Kebakaran',
            desc: 'Ketukan Titir Ganda (Tang-Tang... Tang-Tang...) & sirene damkar',
            btnBg: 'bg-orange-600'
        },
        'medis': {
            icon: '🚑',
            title: 'Darurat Medis / Ambulans',
            desc: 'Sirene Ambulans Dua Nada (WEE-WOO) & ketukan lambat',
            btnBg: 'bg-blue-600'
        },
        'lainnya': {
            icon: '⚠️',
            title: 'Bahaya Lainnya',
            desc: 'Ketukan nada siaga pos ronda & notifikasi peringatan',
            btnBg: 'bg-amber-600'
        }
    };

    function selectPanicCat(category, btn) {
        selectedPanicCategory = category;
        if (typeof activeEmergencyCategory !== 'undefined') {
            activeEmergencyCategory = category;
        }

        const info = wargaCategoryInfo[category] || wargaCategoryInfo['pencurian'];

        document.querySelectorAll('.panic-chip').forEach(c => {
            c.className = 'panic-chip px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition cursor-pointer';
        });
        btn.className = `panic-chip px-3 py-1.5 rounded-xl text-xs font-bold ${info.btnBg} text-white shadow-xs transition cursor-pointer`;

        const iconEl = document.getElementById('w-sound-hint-icon');
        const titleEl = document.getElementById('w-sound-hint-title');
        const descEl = document.getElementById('w-sound-hint-desc');
        if (iconEl) iconEl.innerText = info.icon;
        if (titleEl) titleEl.innerText = info.title;
        if (descEl) descEl.innerText = info.desc;
    }

    function previewWargaCategorySound() {
        startKentonganAlarm(selectedPanicCategory || 'pencurian');
        setTimeout(() => {
            if (isPlayingAudio && document.getElementById('emergency-banner').classList.contains('hidden')) {
                stopEmergencySound();
            }
        }, 4000);
    }

    function triggerWargaPanic() {
        // 1. Evaluasi Kondisi 1: NIK Warga Terverifikasi
        if (!isWargaNikVerified) {
            if (!isWargaLoggedIn) {
                if (confirm("⚠️ VERIFIKASI NIK DIPERLUKAN!\n\nTombol Kentongan Online hanya dapat digunakan jika Anda masuk dengan akun warga yang NIK-nya telah terverifikasi resmi oleh pengurus RW 02.\n\nApakah Anda ingin membuka halaman Masuk sekarang?")) {
                    window.location.href = "{{ route('login') }}";
                }
            } else {
                alert(`⚠️ NIK BELUM TERVERIFIKASI!\n\nAkun Anda (${currentWargaName}) belum diverifikasi oleh pengurus RW/RT. Tombol Kentongan dinonaktifkan.`);
            }
            return;
        }

        // 2. Evaluasi Kondisi 2: Izin Lokasi GPS & Geofence
        if (!locationAcquiredWarga || currentLat === null || currentLon === null) {
            alert("⚠️ LOKASI BELUM DIIZINKAN!\n\nTombol Kentongan Online dinonaktifkan karena lokasi perangkat Anda belum diizinkan atau belum diperoleh.\n\nSistem memerlukan izin lokasi GPS untuk memverifikasi Anda berada di dalam wilayah RW 02 demi mencegah alarm palsu. Silakan izinkan akses lokasi pada peramban.");
            mintaIzinGpsWarga();
            return;
        }

        const distance = calculateHaversineDistance(currentLat, currentLon, rwCenterLat, rwCenterLng);

        // Proteksi sisi klien: jika di luar radius, tolak
        if (distance > rwMaxRadius) {
            alert(`⚠️ PERINGATAN: Posisi Anda berada di luar radius keamanan RW 02 (Jarak: ${distance} meter, Batas aktif: ${rwMaxRadius} meter).\n\nTombol panic hanya dapat digunakan di dalam lingkungan wilayah RW 02. Silakan hubungi langsung nomor kontak darurat posko.`);
            return;
        }

        sendWargaPanicRequest(currentLat, currentLon);
    }

    function sendWargaPanicRequest(lat, lon) {
        // Bunyikan sirene kentongan darurat spesifik kategori
        triggerEmergencyAlert(selectedPanicCategory, `Koordinat: ${lat.toFixed(4)}, ${lon.toFixed(4)}`);

        const feedback = document.getElementById('warga-panic-feedback');
        feedback.innerHTML = `🚨 <strong>SINYAL DARURAT DISIARKAN!</strong> Alarm sirine telah berbunyi di Pos Ronda dan HP pengurus RW. Petugas sedang menuju lokasi Anda!`;
        feedback.classList.remove('hidden');

        fetch('/api/panic', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lon,
                kategori: selectedPanicCategory,
                catatan: `Kentongan Online: ${selectedPanicCategory}`
            })
        })
        .then(async (res) => {
            const data = await res.json();
            if (!res.ok) {
                stopEmergencySound();
                feedback.innerHTML = `🚫 <strong>DITOLAK SISTEM:</strong> ${data.message || 'Permintaan darurat ditolak'}`;
                feedback.className = 'p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in';
            } else {
                const pwaCount = (data.pwa_broadcast && data.pwa_broadcast.pwa_devices_target) ? data.pwa_broadcast.pwa_devices_target : 'seluruh';
                feedback.className = 'p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in';
                feedback.innerHTML = `🚨 <strong>SINYAL DARURAT DISIARKAN!</strong> Notifikasi alarm telah dikirim ke ${pwaCount} perangkat PWA warga & pos ronda!`;
            }
        })
        .catch(err => console.log('Demo panic alert sent'));
    }

    function submitLaporWarga(e) {
        e.preventDefault();
        const judul = document.getElementById('lapor-w-judul').value;
        const deskripsi = document.getElementById('lapor-w-deskripsi').value;
        const feedback = document.getElementById('lapor-w-feedback');

        feedback.innerHTML = '⏳ Meneruskan laporan Anda ke Ketua RT...';
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
            feedback.innerHTML = `✅ <strong>Laporan Berhasil Diterima!</strong> Pengurus RT/RW telah diberitahu.`;
            document.getElementById('form-lapor-warga').reset();
        })
        .catch(() => {
            feedback.innerHTML = `✅ <strong>Laporan Terkirim (Demo Mode)!</strong>`;
            document.getElementById('form-lapor-warga').reset();
        });
    }

    // Toggle Identitas Tamu Warga (WNI = NIK, WNA = Paspor)
    function toggleKewarganegaraanTamuWarga(tipe) {
        const wrapNik = document.getElementById('wrap-tamu-w-nik');
        const wrapPaspor = document.getElementById('wrap-tamu-w-paspor');
        const inputNik = document.getElementById('tamu-w-nik');
        const inputPaspor = document.getElementById('tamu-w-paspor');

        if (tipe === 'WNA') {
            wrapNik.classList.add('hidden');
            wrapPaspor.classList.remove('hidden');
            inputNik.removeAttribute('required');
            inputPaspor.setAttribute('required', 'required');
            inputPaspor.focus();
        } else {
            wrapPaspor.classList.add('hidden');
            wrapNik.classList.remove('hidden');
            inputPaspor.removeAttribute('required');
            inputNik.setAttribute('required', 'required');
            inputNik.focus();
        }
    }

    function submitTamuWarga(e) {
        e.preventDefault();
        const nama = document.getElementById('tamu-w-nama').value.trim();
        const kewarganegaraan = document.querySelector('input[name="tamu_w_kewarganegaraan"]:checked')?.value || 'WNI';
        const nik = document.getElementById('tamu-w-nik')?.value.trim() || '';
        const paspor = document.getElementById('tamu-w-paspor')?.value.trim() || '';
        const hp = document.getElementById('tamu-w-hp').value.trim();
        const alamat = document.getElementById('tamu-w-alamat').value.trim();
        const tujuan = document.getElementById('tamu-w-tujuan').value.trim();
        const keperluan = document.getElementById('tamu-w-keperluan').value.trim();
        const feedback = document.getElementById('tamu-w-feedback');

        if (kewarganegaraan === 'WNI') {
            if (!nik) {
                alert('⚠️ NIK wajib diisi untuk tamu WNI.');
                document.getElementById('tamu-w-nik').focus();
                return;
            }
            if (nik.length !== 16 || !/^\d+$/.test(nik)) {
                if (!confirm('⚠️ NIK standar berjumlah 16 digit angka. Lanjutkan dengan NIK ini?')) {
                    document.getElementById('tamu-w-nik').focus();
                    return;
                }
            }
        } else {
            if (!paspor) {
                alert('⚠️ Nomor Paspor / Dokumen Imigrasi wajib diisi untuk tamu WNA.');
                document.getElementById('tamu-w-paspor').focus();
                return;
            }
        }

        feedback.innerHTML = '⏳ Menyimpan data tamu wajib lapor 2x24 jam...';
        feedback.className = 'p-3 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-semibold text-center animate-in fade-in block';
        feedback.classList.remove('hidden');

        fetch('/api/buku-tamu', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                nama_tamu: nama,
                kewarganegaraan: kewarganegaraan,
                nik: kewarganegaraan === 'WNI' ? nik : null,
                nomor_paspor: kewarganegaraan === 'WNA' ? paspor : null,
                no_hp: hp,
                alamat_asal: alamat,
                warga_yang_dikunjungi: tujuan,
                tujuan_kunjungan: keperluan
            })
        })
        .then(async (res) => {
            const data = await res.json();
            if (res.ok && data.success) {
                const identitasText = kewarganegaraan === 'WNA' ? `Paspor: ${paspor}` : `NIK: ${nik}`;
                feedback.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in block';
                feedback.innerHTML = `✅ <strong>Pendaftaran Tamu Berhasil!</strong> Tamu <strong>${nama}</strong> (${kewarganegaraan} • ${identitasText}) telah tercatat dalam sistem 2x24 jam.`;
                document.getElementById('form-tamu-warga').reset();
                toggleKewarganegaraanTamuWarga('WNI');
            } else {
                feedback.className = 'p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in block';
                feedback.innerHTML = `⚠️ <strong>Pendaftaran Gagal:</strong> ${data.message || 'Terjadi kesalahan sistem'}`;
            }
        })
        .catch((err) => {
            feedback.className = 'p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in block';
            feedback.innerHTML = `⚠️ <strong>Kesalahan Jaringan:</strong> ${err.message}`;
        });
    }
</script>
@endpush
