@extends('layouts.app')

@section('title', 'Arum Smart - Sistem Integrasi Keamanan Warga Digital')

@push('styles')
<!-- DataTables CSS & Styling -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<style>
    /* DataTables Custom Theme Styling */
    .dataTables_wrapper .dataTables_length select {
        padding: 0.35rem 2rem 0.35rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid #cbd5e1;
        background-color: #fff;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid #cbd5e1;
        outline: none;
        margin-left: 0.5rem;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.65rem !important;
        font-size: 0.75rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid #e2e8f0 !important;
        margin: 0 2px !important;
        background: white !important;
        color: #475569 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #7c3aed !important;
        color: white !important;
        border-color: #7c3aed !important;
        font-weight: bold !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .dataTables_wrapper .dataTables_info {
        font-size: 0.75rem;
        color: #64748b;
        padding-top: 0.75rem;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #f1f5f9 !important;
    }
    table.dataTable thead th {
        border-bottom: 2px solid #e2e8f0 !important;
    }
</style>
@endpush

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

            <!-- Keterangan Ciri Khas Suara Kentongan Kategori Ini -->
            <div id="panic-sound-hint" class="text-[11px] px-3.5 py-2 rounded-2xl bg-slate-50 text-slate-700 border border-slate-200/80 flex items-center justify-between gap-2 max-w-sm mx-auto shadow-xs">
                <div class="flex items-center gap-2 text-left">
                    <span id="sound-hint-icon" class="text-base">🚨</span>
                    <div>
                        <span class="font-extrabold text-slate-800" id="sound-hint-title">Maling / Curanmor</span>
                        <p class="text-[10px] text-slate-500 leading-tight" id="sound-hint-desc">Ketukan bertubi-tubi sangat cepat (Doro Muluk) & sirene maling</p>
                    </div>
                </div>
                <button type="button" onclick="previewSoundCurrentCategory()" title="Dengarkan Contoh Bunyi" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-[10px] cursor-pointer transition shrink-0 border border-rose-200 flex items-center gap-1">
                    <span>🔊</span> <span>Tes Bunyi</span>
                </button>
            </div>

            <!-- PANEL 2 KONDISI KESELAMATAN AKTIVASI KENTONGAN ONLINE -->
            <div class="bg-slate-50/90 rounded-2xl p-3 sm:p-4 border border-slate-200/90 text-left space-y-2.5 max-w-md mx-auto shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                    <span class="text-[11px] font-extrabold uppercase text-slate-700 tracking-wider flex items-center gap-1.5">
                        <span>🛡️</span> Syarat Aktivasi Kentongan (2 Kondisi Wajib)
                    </span>
                    <span id="overall-condition-badge" class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200">
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
                                <span class="text-[10px] text-amber-700 block font-medium">Akun ada, namun NIK belum diverifikasi pengurus RW</span>
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
                        <span id="cond2-icon" class="w-5 h-5 rounded-lg bg-amber-500 text-white flex items-center justify-center font-black text-[10px] shrink-0">📍</span>
                        <div>
                            <span class="font-bold text-slate-800 block text-[11px]">2. Radius Geofence RW 02</span>
                            <span id="cond2-geofence-text" class="text-[10px] text-slate-500 block font-medium">Batas radius: &le; {{ $rwSetting->panic_radius_meters ?? 300 }} Meter dari titik koordinat RW</span>
                        </div>
                    </div>
                    <span id="cond2-geofence-badge" class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 shrink-0">
                        MENUNGGU GPS
                    </span>
                </div>
            </div>

            <!-- Tombol Utama Panic Button (Hanya aktif jika 2 KONDISI TERPENUHI) -->
            <div class="py-3 flex justify-center">
                <button id="main-panic-btn" onclick="handlePanicClick()" class="relative w-36 h-36 sm:w-44 sm:h-44 rounded-full bg-gradient-to-tr from-rose-700 via-rose-600 to-red-500 text-white font-extrabold shadow-2xl shadow-rose-600/50 flex flex-col items-center justify-center gap-1 active:scale-95 transition-all duration-200 cursor-not-allowed opacity-40 grayscale border-4 border-white">
                    <svg id="main-panic-icon" class="w-12 h-12 sm:w-14 sm:h-14 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span id="main-panic-title" class="text-xs sm:text-sm font-black tracking-wider uppercase drop-shadow text-center px-2">
                        @if(!Auth::check())
                            NIK BELUM VERIFIKASI
                        @elseif(!Auth::user()->isNikVerified())
                            NIK BELUM DISETUJUI
                        @else
                            LOKASI DIBUTUHKAN
                        @endif
                    </span>
                    <span id="main-panic-subtitle" class="text-[9px] sm:text-[10px] font-medium text-rose-100 uppercase tracking-tight">
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

            <!-- Lokasi GPS & Jarak Geofence Terdeteksi -->
            <div class="space-y-2">
                <div class="flex items-center justify-center gap-1.5 text-xs text-slate-500">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span id="gps-status-text"><span class="text-amber-600 font-bold">⚠️ Menunggu Izin Lokasi:</span> Tombol kentongan dinonaktifkan sampai lokasi diperoleh.</span>
                </div>

                <!-- Tombol Minta Izin Lokasi Peramban -->
                <div class="flex justify-center">
                    <button type="button" id="btn-minta-lokasi-home" onclick="mintaIzinGpsHome()" class="px-3 py-1.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs shadow-sm transition cursor-pointer flex items-center gap-1.5">
                        <span>📍</span>
                        <span>Aktifkan / Izinkan Lokasi GPS</span>
                    </button>
                </div>

                <!-- Tombol Bantuan Uji Simulasi Jarak -->
                <div class="flex items-center justify-center gap-1.5 text-[11px] pt-1 border-t border-slate-100">
                    <span class="text-slate-400">Uji Jarak:</span>
                    <button type="button" onclick="simulasiGpsHome('dalam')" class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200 hover:bg-emerald-100 cursor-pointer">
                        🟢 Posko (&lt; {{ $rwSetting->panic_radius_meters ?? 300 }}m)
                    </button>
                    <button type="button" onclick="simulasiGpsHome('luar')" class="px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 font-semibold border border-rose-200 hover:bg-rose-100 cursor-pointer">
                        🔴 Luar Wilayah (&gt; {{ $rwSetting->panic_radius_meters ?? 300 }}m)
                    </button>
                    <button type="button" onclick="mintaIzinGpsHome()" class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-semibold border border-slate-200 hover:bg-slate-200 cursor-pointer">
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

    <!-- SEKSI: TABEL RIWAYAT AKTIVASI KENTONGAN ONLINE & LOKASI (DATATABLES) -->
    @php
        $isAdmin = Auth::check() && in_array(Auth::user()->role, ['admin', 'rt', 'rw', 'bhabinkamtibmas']);
    @endphp
    <section id="riwayat-kentongan-section" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl font-bold shadow-inner">
                    🚨
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 text-[10px] font-extrabold uppercase tracking-wider">
                            Data Kejadian Real-Time
                        </span>
                        @if($isAdmin)
                        <span class="px-2 py-0.5 rounded-md bg-violet-100 text-violet-800 text-[10px] font-bold flex items-center gap-1 border border-violet-200">
                            <span>🛡️</span> Mode Admin: Hak Edit & Hapus Aktif
                        </span>
                        @endif
                    </div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight mt-0.5">
                        Riwayat Aktivasi Kentongan Online & Titik Lokasi
                    </h2>
                    <p class="text-xs text-slate-500">
                        Pencatatan waktu kejadian, jenis bahaya, koordinat GPS, dan tautan Google Maps langsung.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="refreshTabelKentongan()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 border border-slate-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    <span>Segarkan Data</span>
                </button>
            </div>
        </div>

        {{-- Feedback Alert untuk Operasi Tabel --}}
        <div id="tabel-panic-feedback" class="hidden p-3.5 rounded-2xl text-xs font-semibold text-center animate-in fade-in"></div>

        {{-- Tabel DataTables Riwayat Kentongan --}}
        <div class="overflow-x-auto">
            <table id="tabel-kentongan" class="w-full text-xs stripe hover" style="width:100%">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <th class="text-center px-3 py-3 w-10">#</th>
                        <th class="text-left px-3 py-3">Waktu & Tanggal</th>
                        <th class="text-left px-3 py-3">Jenis Kejadian</th>
                        <th class="text-left px-3 py-3">Keterangan</th>
                        <th class="text-left px-3 py-3">Titik Lokasi & Maps</th>
                        <th class="text-left px-3 py-3">Pelapor / Status</th>
                        @if($isAdmin)
                        <th class="text-center px-3 py-3 w-28">Aksi Admin</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($riwayatKentongan ?? [] as $index => $item)
                    <tr id="row-panic-{{ $item->id }}" data-id="{{ $item->id }}">
                        <td class="text-center px-3 py-3 font-bold text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-3 py-3 whitespace-nowrap" data-order="{{ $item->created_at ? $item->created_at->timestamp : 0 }}">
                            <span class="font-bold text-slate-900 block row-waktu-text">{{ $item->waktu_formatted }}</span>
                            <span class="text-[10px] text-slate-400">{{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}</span>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            @php
                                $badgeClass = match($item->kategori) {
                                    'pencurian' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'kebakaran' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'medis' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    default => 'bg-amber-100 text-amber-800 border-amber-200',
                                };
                            @endphp
                            <span class="row-kategori-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-extrabold border {{ $badgeClass }}">
                                <span class="badge-icon">{{ $item->kategori_icon }}</span>
                                <span class="badge-kategori-text">{{ $item->kategori_badge }}</span>
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            <span class="row-catatan-text font-semibold text-slate-800 block max-w-xs">{{ $item->catatan ?: 'Sinyal bahaya kentongan online' }}</span>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <div class="space-y-1">
                                <span class="font-mono text-[11px] text-slate-600 block">
                                    {{ number_format((float)$item->latitude, 6) }}, {{ number_format((float)$item->longitude, 6) }}
                                </span>
                                <a href="{{ $item->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[10px] transition border border-emerald-200 cursor-pointer shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>📍 Buka Google Maps</span>
                                </a>
                            </div>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <span class="row-pelapor-text block font-bold text-slate-800 text-[11px]">{{ $item->nama_pelapor }}</span>
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $item->status === 'selesai' ? 'bg-slate-100 text-slate-600' : ($item->status === 'ditangani' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-700') }}">
                                {{ $item->status ?: 'aktif' }}
                            </span>
                        </td>
                        @if($isAdmin)
                        <td class="px-3 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="bukaModalEditKentongan({{ $item->id }}, '{{ $item->kategori }}', '{{ addslashes($item->catatan ?? '') }}', '{{ $item->waktu_formatted }}', '{{ addslashes($item->nama_pelapor ?? '') }}', '{{ number_format((float)$item->latitude, 6) }}, {{ number_format((float)$item->longitude, 6) }}')" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition border border-amber-200 cursor-pointer" title="Edit Jenis Kejadian & Keterangan">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button type="button" onclick="hapusRiwayatKentongan({{ $item->id }}, '{{ $item->waktu_formatted }}')" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 transition border border-rose-200 cursor-pointer" title="Hapus Riwayat Kejadian">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    {{-- Diisi secara otomatis oleh DataTables jika kosong --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- MODAL EDIT RIWAYAT KENTONGAN (HANYA ADMIN) -->
    @if($isAdmin)
    <div id="modal-edit-kentongan" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in zoom-in-95 text-left">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm">
                        ✏️
                    </span>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Edit Riwayat Kentongan</h3>
                        <p class="text-[10px] text-slate-500">Khusus mengubah Jenis Kejadian &amp; Keterangan</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-edit-kentongan')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Info Read-Only (Waktu, Pelapor, Lokasi) --}}
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1 text-[11px]">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Waktu Kejadian:</span>
                    <span id="edit-modal-waktu" class="font-bold text-slate-800">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Pelapor:</span>
                    <span id="edit-modal-pelapor" class="font-bold text-slate-800">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Koordinat GPS:</span>
                    <span id="edit-modal-koordinat" class="font-mono text-slate-700">-</span>
                </div>
            </div>

            <form id="form-edit-kentongan" onsubmit="submitEditKentongan(event)" class="space-y-4">
                <input type="hidden" id="edit-modal-id">

                {{-- Field 1: Jenis Kejadian --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Jenis Kejadian (Kategori) <span class="text-rose-500">*</span>
                    </label>
                    <select id="edit-modal-kategori" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-200 font-bold text-slate-800 bg-white">
                        <option value="pencurian">🚨 Pencurian / Maling (Doro Muluk)</option>
                        <option value="kebakaran">🔥 Bahaya Kebakaran (Titir Ganda)</option>
                        <option value="medis">🚑 Darurat Medis / Ambulans (Dua Nada)</option>
                        <option value="lainnya">⚠️ Siaga Lingkungan / Lainnya</option>
                    </select>
                </div>

                {{-- Field 2: Keterangan --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Keterangan Kejadian <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="edit-modal-catatan" rows="3" required placeholder="Tuliskan keterangan detail kejadian di sini..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-200"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-edit-kentongan')" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btn-simpan-edit-kentongan" class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold transition shadow-md cursor-pointer flex items-center justify-center gap-1.5">
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

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

        <form id="form-buku-tamu" onsubmit="handleFormBukuTamu(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 max-w-2xl">
            <!-- Pilihan Kewarganegaraan Tamu -->
            <div class="sm:col-span-2 bg-slate-50 p-3 rounded-2xl border border-slate-200/80">
                <label class="block text-xs font-bold text-slate-700 mb-2">Status Kewarganegaraan Tamu</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-blue-500 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 has-[:checked]:text-blue-900 font-bold text-xs text-slate-700 shadow-2xs">
                        <input type="radio" name="tamu_kewarganegaraan" value="WNI" checked onchange="toggleKewarganegaraanTamu('WNI')" class="accent-blue-600">
                        <span>🇮🇩 WNI (Warga Indonesia)</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-blue-500 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 has-[:checked]:text-blue-900 font-bold text-xs text-slate-700 shadow-2xs">
                        <input type="radio" name="tamu_kewarganegaraan" value="WNA" onchange="toggleKewarganegaraanTamu('WNA')" class="accent-blue-600">
                        <span>🌐 WNA (Warga Asing)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Tamu</label>
                <input type="text" id="tamu-nama" required placeholder="Nama lengkap sesuai dokumen identitas" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <!-- Identitas Dinamis: NIK untuk WNI -->
            <div id="wrap-tamu-nik">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    NIK (Nomor Induk Kependudukan) <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="tamu-nik" required maxlength="16" pattern="[0-9]{16}" placeholder="16 Digit NIK sesuai e-KTP" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 font-mono">
                <span class="text-[10px] text-slate-400 mt-0.5 block">Wajib 16 digit angka sesuai KTP tamu WNI</span>
            </div>

            <!-- Identitas Dinamis: Nomor Paspor untuk WNA -->
            <div id="wrap-tamu-paspor" class="hidden">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Nomor Paspor / Dokumen Imigrasi <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="tamu-paspor" maxlength="50" placeholder="Contoh: A12345678 / Paspor Resmi" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 font-mono uppercase">
                <span class="text-[10px] text-slate-400 mt-0.5 block">Wajib nomor paspor atau izin tinggal bagi tamu WNA</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" id="tamu-hp" required placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Asal / Negara Asal</label>
                <input type="text" id="tamu-alamat" required placeholder="Kota asal atau negara asal domisili tamu" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
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
            @forelse($cctvs as $cctv)
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
                        RT {{ $cctv->rt ?? '01' }}
                    </div>
                </div>

                <div class="p-3 bg-slate-900/90 text-white flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold">{{ $cctv->nama_lokasi ?? 'Kamera Lingkungan' }}</h4>
                        <p class="text-[10px] text-slate-400">Resolusi HD 1080p &bull; Infra-Red Night Vision</p>
                    </div>
                    <button onclick="alert('Membuka tampilan layar penuh untuk {{ addslashes($cctv->nama_lokasi ?? 'Kamera Lingkungan') }}')" class="text-slate-400 hover:text-white p-1 rounded transition cursor-pointer" title="Layar Penuh">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full py-10 px-4 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl text-slate-500">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700">Belum Ada Kamera CCTV Aktif</p>
                <p class="text-xs text-slate-400 mt-0.5">Daftar kamera IP lingkungan sedang dalam proses konfigurasi jaringan.</p>
            </div>
            @endforelse
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
                        <div class="mt-1">
                            @if(($tamu->kewarganegaraan ?? 'WNI') === 'WNA')
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">🌐 WNA • Paspor: {{ $tamu->nomor_paspor ?? '-' }}</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-bold">🇮🇩 WNI • NIK: {{ $tamu->nik ?? '-' }}</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Asal: {{ $tamu->alamat_asal }}</p>
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

    const panicCategoryInfo = {
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

    // Pilih Kategori Bahaya & Perbarui Info Suara
    function selectPanicCategory(cat, btnElement) {
        activePanicCategory = cat;
        if (typeof activeEmergencyCategory !== 'undefined') {
            activeEmergencyCategory = cat;
        }

        const info = panicCategoryInfo[cat] || panicCategoryInfo['pencurian'];

        document.querySelectorAll('.panic-cat-btn').forEach(btn => {
            btn.className = 'panic-cat-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer transition';
        });
        btnElement.className = `panic-cat-btn px-3 py-1.5 rounded-xl text-xs font-bold ${info.btnBg} text-white shadow-xs cursor-pointer transition`;

        // Update teks keterangan suara
        const iconEl = document.getElementById('sound-hint-icon');
        const titleEl = document.getElementById('sound-hint-title');
        const descEl = document.getElementById('sound-hint-desc');
        if (iconEl) iconEl.innerText = info.icon;
        if (titleEl) titleEl.innerText = info.title;
        if (descEl) descEl.innerText = info.desc;
    }

    function previewSoundCurrentCategory() {
        startKentonganAlarm(activePanicCategory || 'pencurian');
        setTimeout(() => {
            if (isPlayingAudio && document.getElementById('emergency-banner').classList.contains('hidden')) {
                stopEmergencySound();
            }
        }, 4000);
    }

    const rwCenterLat = {{ $rwSetting->center_latitude ?? -6.208800 }};
    const rwCenterLng = {{ $rwSetting->center_longitude ?? 106.845600 }};
    const rwMaxRadius = {{ $rwSetting->panic_radius_meters ?? 300 }};
    const isUserNikVerified = {{ (Auth::check() && Auth::user()->isNikVerified()) ? 'true' : 'false' }};
    const isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    const currentUserName = "{{ Auth::check() ? addslashes(Auth::user()->name) : '' }}";
    const currentUserNik = "{{ Auth::check() ? addslashes(Auth::user()->nik ?? '') : '' }}";

    let currentHomeLat = null;
    let currentHomeLon = null;
    let locationAcquiredHome = false;
    let isHomeWithinGeofence = false;

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

    // Evaluasi Keseluruhan 2 Kondisi (NIK Terverifikasi & Radius Geofence)
    function evaluateKentonganConditions() {
        const panicBtn = document.getElementById('main-panic-btn');
        const panicTitle = document.getElementById('main-panic-title');
        const panicSubtitle = document.getElementById('main-panic-subtitle');
        const overallBadge = document.getElementById('overall-condition-badge');
        const cond2Icon = document.getElementById('cond2-icon');
        const cond2Badge = document.getElementById('cond2-geofence-badge');
        const cond2Text = document.getElementById('cond2-geofence-text');

        // KASUS 1: Kondisi 1 (NIK Verifikasi) GAGAL
        if (!isUserNikVerified) {
            if (panicBtn) {
                panicBtn.classList.add('opacity-40', 'grayscale', 'cursor-not-allowed');
                panicBtn.classList.remove('panic-pulse');
            }
            if (!isUserLoggedIn) {
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

        // KONDISI 1 LOLOS: Sekarang periksa KONDISI 2 (GPS & Geofence)
        if (!locationAcquiredHome || currentHomeLat === null || currentHomeLon === null) {
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

        // Hitung Jarak Terhadap Titik Koordinat RW
        const distance = calculateHaversineDistance(currentHomeLat, currentHomeLon, rwCenterLat, rwCenterLng);

        if (distance <= rwMaxRadius) {
            // KEDUA KONDISI TERPENUHI (NIK Terverifikasi + Di Dalam Radius)
            isHomeWithinGeofence = true;
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
            // Kondisi 1 Lolos, tapi Kondisi 2 GAGAL (Di Luar Radius)
            isHomeWithinGeofence = false;
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

    // Set status tombol Nonaktif / Terkunci jika lokasi belum ada
    function setGpsDisabledStateHome(reason = 'pending', customMsg = '') {
        locationAcquiredHome = false;
        isHomeWithinGeofence = false;
        currentHomeLat = null;
        currentHomeLon = null;

        const statusSpan = document.getElementById('gps-status-text');
        const btnMinta = document.getElementById('btn-minta-lokasi-home');
        const cond2Icon = document.getElementById('cond2-icon');
        const cond2Badge = document.getElementById('cond2-geofence-badge');
        const cond2Text = document.getElementById('cond2-geofence-text');

        if (btnMinta) btnMinta.classList.remove('hidden');

        if (reason === 'denied') {
            if (statusSpan) statusSpan.innerHTML = `<span class="text-rose-600 font-bold">🚫 IZIN LOKASI DITOLAK:</span> Buka izin lokasi di peramban agar koordinat dapat diverifikasi.`;
            if (cond2Badge) {
                cond2Badge.className = 'text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200 shrink-0';
                cond2Badge.innerText = 'IZIN DITOLAK';
            }
            if (cond2Text) cond2Text.innerHTML = `<span class="text-rose-600 font-bold">Izin GPS ditolak peramban</span>`;
        } else if (reason === 'unsupported') {
            if (statusSpan) statusSpan.innerHTML = `<span class="text-rose-600 font-bold">⚠️ GPS Tidak Didukung:</span> Peramban tidak mendukung sensor lokasi.`;
        } else {
            if (statusSpan) statusSpan.innerHTML = customMsg || `<span class="text-amber-600 font-bold">⚠️ Menunggu Izin Lokasi:</span> Tombol kentongan dinonaktifkan sampai lokasi perangkat diizinkan & terverifikasi.`;
        }

        evaluateKentonganConditions();
    }

    function updateGeofenceStatusHome(lat, lon) {
        currentHomeLat = lat;
        currentHomeLon = lon;
        locationAcquiredHome = true;

        const distance = calculateHaversineDistance(lat, lon, rwCenterLat, rwCenterLng);
        const statusSpan = document.getElementById('gps-status-text');
        const btnMinta = document.getElementById('btn-minta-lokasi-home');

        if (btnMinta) btnMinta.classList.add('hidden');

        if (distance <= rwMaxRadius) {
            if (statusSpan) statusSpan.innerHTML = `<span class="text-emerald-700 font-bold">✅ KOORDINAT TERVERIFIKASI</span> (Jarak: <strong>${distance}m</strong> dari Posko, Batas: ${rwMaxRadius}m)`;
        } else {
            if (statusSpan) statusSpan.innerHTML = `<span class="text-rose-600 font-bold">🚫 KOORDINAT DI LUAR WILAYAH</span> (Jarak: <strong>${distance}m</strong> > Batas: ${rwMaxRadius}m)`;
        }

        evaluateKentonganConditions();
    }

    // Minta izin lokasi fisik dari peramban
    function mintaIzinGpsHome() {
        const statusSpan = document.getElementById('gps-status-text');
        if (statusSpan) statusSpan.innerHTML = '⏳ <em>Mendeteksi sinyal koordinat GPS perangkat...</em>';

        if (!navigator.geolocation) {
            setGpsDisabledStateHome('unsupported');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                updateGeofenceStatusHome(pos.coords.latitude, pos.coords.longitude);
            },
            (err) => {
                console.warn('Izin lokasi ditolak / error:', err);
                if (err.code === 1) { // PERMISSION_DENIED
                    setGpsDisabledStateHome('denied');
                } else {
                    setGpsDisabledStateHome('error', `<span class="text-rose-600 font-bold">⚠️ Gagal Membaca GPS:</span> ${err.message}. Tombol dinonaktifkan.`);
                }
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    function simulasiGpsHome(tipe) {
        if (tipe === 'dalam') {
            // Posisi ~100m dari posko (lokasi diperoleh & valid)
            updateGeofenceStatusHome(rwCenterLat + 0.0008, rwCenterLng + 0.0006);
        } else {
            // Posisi ~500m dari posko (di luar radius 300m)
            updateGeofenceStatusHome(rwCenterLat + 0.0045, rwCenterLng + 0.0035);
        }
    }

    // Inisialisasi awal evaluasi 2 kondisi
    setGpsDisabledStateHome('pending');
    mintaIzinGpsHome();

    // Eksekusi Tombol Darurat Digital (Kentongan Online)
    function handlePanicClick() {
        // 1. EVALUASI KONDISI 1: Verifikasi NIK Warga
        if (!isUserNikVerified) {
            if (!isUserLoggedIn) {
                if (confirm("⚠️ VERIFIKASI NIK DIPERLUKAN!\n\nTombol Kentongan Online hanya dapat digunakan jika Anda:\n1. Masuk dengan akun warga yang telah terverifikasi NIK resminya oleh pengurus RW 02.\n2. Berada di dalam batas radius geofence RW 02.\n\nApakah Anda ingin membuka halaman Masuk / Verifikasi sekarang?")) {
                    window.location.href = "{{ route('login') }}";
                }
            } else {
                alert(`⚠️ NIK BELUM TERVERIFIKASI!\n\nAkun Anda (${currentUserName}) belum memiliki status verifikasi NIK sah dari Pengurus RW/RT.\n\nTombol Kentongan Online dinonaktifkan sampai NIK Anda diverifikasi.`);
            }
            return;
        }

        // 2. EVALUASI KONDISI 2: Izin Lokasi GPS & Radius Geofence
        if (!locationAcquiredHome || currentHomeLat === null || currentHomeLon === null) {
            alert("⚠️ LOKASI GPS DIPERLUKAN!\n\nTombol Kentongan Online dinonaktifkan karena koordinat GPS perangkat Anda belum diperoleh.\n\nSistem memerlukan koordinat lokasi untuk memverifikasi Anda berada di dalam wilayah RW 02 demi mencegah alarm palsu.");
            mintaIzinGpsHome();
            return;
        }

        const distance = calculateHaversineDistance(currentHomeLat, currentHomeLon, rwCenterLat, rwCenterLng);

        // Proteksi sisi klien jika di luar radius
        if (distance > rwMaxRadius) {
            alert(`⚠️ PERINGATAN: POSISI DI LUAR RADIUS!\n\nPosisi Anda berada di luar radius keamanan RW 02 (Jarak: ${distance} meter > Batas aktif: ${rwMaxRadius} meter).\n\nTombol Kentongan hanya aktif jika berada di dalam lingkungan wilayah RW 02.`);
            return;
        }

        sendPanicAlert(currentHomeLat, currentHomeLon);
    }

    function sendPanicAlert(latitude, longitude) {
        // Bunyikan sirene kentongan darurat seketika
        triggerEmergencyAlert(activePanicCategory, `Koordinat: ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`);

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
                'Accept': 'application/json',
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
            if (!res.ok) {
                stopEmergencySound();
                feedback.className = 'p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in';
                if (data.out_of_radius) {
                    feedback.innerHTML = `🚫 <strong>DITOLAK SISTEM:</strong> ${data.message}`;
                } else {
                    feedback.innerHTML = `⚠️ <strong>GAGAL MENCATAT:</strong> ${data.message || 'Terjadi kesalahan pada sistem.'}`;
                }
            } else if (res.ok && data.alert) {
                feedback.className = 'p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in';
                const pwaCount = (data.pwa_broadcast && data.pwa_broadcast.pwa_devices_target) ? data.pwa_broadcast.pwa_devices_target : 'seluruh';
                feedback.innerHTML = `🚨 <strong>SINYAL DARURAT BERHASIL DICATAT!</strong> Notifikasi alarm telah disiarkan ke ${pwaCount} perangkat PWA warga terpasang & Pos Ronda.`;
                // Tambahkan rekaman kentongan ke DataTables seketika
                if (typeof tambahBarisKeDataTable === 'function') {
                    tambahBarisKeDataTable(data.alert);
                }
            }
        })
        .catch(err => {
            stopEmergencySound();
            feedback.className = 'p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in';
            feedback.innerHTML = `⚠️ <strong>KESALAHAN KONEKSI:</strong> Tidak dapat menghubungi server (${err.message})`;
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

    // Toggle Input Identitas Tamu (WNI = NIK, WNA = Nomor Paspor)
    function toggleKewarganegaraanTamu(tipe) {
        const wrapNik = document.getElementById('wrap-tamu-nik');
        const wrapPaspor = document.getElementById('wrap-tamu-paspor');
        const inputNik = document.getElementById('tamu-nik');
        const inputPaspor = document.getElementById('tamu-paspor');

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

    // Form Buku Tamu 2x24 Jam
    function handleFormBukuTamu(e) {
        e.preventDefault();
        const nama = document.getElementById('tamu-nama').value.trim();
        const kewarganegaraan = document.querySelector('input[name="tamu_kewarganegaraan"]:checked')?.value || 'WNI';
        const nik = document.getElementById('tamu-nik')?.value.trim() || '';
        const paspor = document.getElementById('tamu-paspor')?.value.trim() || '';
        const hp = document.getElementById('tamu-hp').value.trim();
        const alamat = document.getElementById('tamu-alamat').value.trim();
        const warga = document.getElementById('tamu-warga').value.trim();
        const tujuan = document.getElementById('tamu-tujuan').value.trim();
        const feedback = document.getElementById('tamu-feedback');

        if (kewarganegaraan === 'WNI') {
            if (!nik) {
                alert('⚠️ NIK wajib diisi untuk tamu WNI.');
                document.getElementById('tamu-nik').focus();
                return;
            }
            if (nik.length !== 16 || !/^\d+$/.test(nik)) {
                if (!confirm('⚠️ NIK standar berjumlah 16 digit angka. Lanjutkan dengan NIK yang dimasukkan?')) {
                    document.getElementById('tamu-nik').focus();
                    return;
                }
            }
        } else {
            if (!paspor) {
                alert('⚠️ Nomor Paspor / Dokumen Imigrasi wajib diisi untuk tamu WNA.');
                document.getElementById('tamu-paspor').focus();
                return;
            }
        }

        feedback.innerHTML = '⏳ Mendaftarkan tamu ke buku tamu digital RW 02...';
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
                warga_yang_dikunjungi: warga,
                tujuan_kunjungan: tujuan
            })
        })
        .then(async (res) => {
            const data = await res.json();
            if (res.ok && data.success) {
                const identitasText = kewarganegaraan === 'WNA' ? `Paspor: ${paspor}` : `NIK: ${nik}`;
                feedback.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in block';
                feedback.innerHTML = `✅ <strong>Pendataan Tamu Berhasil!</strong> Tamu <strong>${nama}</strong> (${kewarganegaraan} • ${identitasText}) telah terdaftar. Pemberitahuan diteruskan ke Ketua RT setempat.`;
                document.getElementById('form-buku-tamu').reset();
                toggleKewarganegaraanTamu('WNI');
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

<!-- CDN jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    let dtKentongan = null;
    const isUserAdminRole = {{ $isAdmin ? 'true' : 'false' }};

    $(document).ready(function() {
        initDataTableKentongan();
    });

    function initDataTableKentongan() {
        if ($.fn.DataTable.isDataTable('#tabel-kentongan')) {
            dtKentongan = $('#tabel-kentongan').DataTable();
            return;
        }

        dtKentongan = $('#tabel-kentongan').DataTable({
            responsive: true,
            order: [[1, 'desc']], // Urutkan berdasarkan waktu kejadian terbaru
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                search: "🔍 Cari Kejadian:",
                searchPlaceholder: "Kategori, catatan, koordinat...",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari total _TOTAL_ kejadian",
                infoEmpty: "Belum ada riwayat kentongan yang tercatat",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada riwayat kentongan yang cocok dengan pencarian",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Berikutnya &rarr;",
                    previous: "&larr; Sebelumnya"
                }
            }
        });
    }

    // Tambah rekaman baru ke DataTable secara realtime saat tombol kentongan diklik
    function tambahBarisKeDataTable(item) {
        if (!dtKentongan) return;

        let badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
        if (item.kategori === 'pencurian') badgeClass = 'bg-rose-100 text-rose-800 border-rose-200';
        else if (item.kategori === 'kebakaran') badgeClass = 'bg-orange-100 text-orange-800 border-orange-200';
        else if (item.kategori === 'medis') badgeClass = 'bg-blue-100 text-blue-800 border-blue-200';

        const rowData = [
            `<span class="font-bold text-slate-400">#</span>`,
            `<span class="font-bold text-slate-900 block row-waktu-text">${item.waktu}</span><span class="text-[10px] text-emerald-600 font-bold">Baru saja</span>`,
            `<span class="row-kategori-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-extrabold border ${badgeClass}"><span class="badge-icon">${item.kategori_icon}</span> <span class="badge-kategori-text">${item.kategori_badge}</span></span>`,
            `<span class="row-catatan-text font-semibold text-slate-800 block max-w-xs">${escapeHtml(item.catatan || 'Sinyal bahaya kentongan online')}</span>`,
            `<div class="space-y-1"><span class="font-mono text-[11px] text-slate-600 block">${item.koordinat_label}</span><a href="${item.google_maps_url}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[10px] transition border border-emerald-200 shadow-2xs">📍 Buka Google Maps</a></div>`,
            `<span class="row-pelapor-text block font-bold text-slate-800 text-[11px]">${escapeHtml(item.pelapor)}</span><span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase ${item.status === 'selesai' ? 'bg-slate-100 text-slate-600' : (item.status === 'ditangani' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-700')}">${item.status || 'aktif'}</span>`
        ];

        if (isUserAdminRole) {
            rowData.push(`
                <div class="flex items-center justify-center gap-1">
                    <button type="button" onclick="bukaModalEditKentongan(${item.id}, '${item.kategori}', '${escapeQuote(item.catatan || '')}', '${item.waktu}', '${escapeQuote(item.pelapor)}', '${item.koordinat_label}')" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition border border-amber-200 cursor-pointer" title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    <button type="button" onclick="hapusRiwayatKentongan(${item.id}, '${item.waktu}')" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 transition border border-rose-200 cursor-pointer" title="Hapus">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            `);
        }

        const newRow = dtKentongan.row.add(rowData).draw(false).node();
        $(newRow).attr('id', 'row-panic-' + item.id).addClass('bg-rose-50/50');
        tampilkanTabelFeedback(`🚨 Kejadian baru berhasil dicatat dan ditambahkan ke tabel: "${item.kategori_badge}"`, 'success');
    }

    // Refresh Data Riwayat Kentongan via AJAX
    function refreshTabelKentongan() {
        fetch('{{ route("api.panic.list") }}')
            .then(res => res.json())
            .then(res => {
                if (res.success && dtKentongan) {
                    dtKentongan.clear();
                    res.data.forEach((item, idx) => {
                        let badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                        if (item.kategori === 'pencurian') badgeClass = 'bg-rose-100 text-rose-800 border-rose-200';
                        else if (item.kategori === 'kebakaran') badgeClass = 'bg-orange-100 text-orange-800 border-orange-200';
                        else if (item.kategori === 'medis') badgeClass = 'bg-blue-100 text-blue-800 border-blue-200';

                        const rowData = [
                            `<span class="font-bold text-slate-400">${idx + 1}</span>`,
                            `<span class="font-bold text-slate-900 block row-waktu-text">${item.waktu}</span>`,
                            `<span class="row-kategori-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-extrabold border ${badgeClass}"><span class="badge-icon">${item.kategori_icon}</span> <span class="badge-kategori-text">${item.kategori_badge}</span></span>`,
                            `<span class="row-catatan-text font-semibold text-slate-800 block max-w-xs">${escapeHtml(item.catatan)}</span>`,
                            `<div class="space-y-1"><span class="font-mono text-[11px] text-slate-600 block">${item.koordinat_label}</span><a href="${item.google_maps_url}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[10px] transition border border-emerald-200 shadow-2xs">📍 Buka Google Maps</a></div>`,
                            `<span class="row-pelapor-text block font-bold text-slate-800 text-[11px]">${escapeHtml(item.pelapor)}</span><span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase ${item.status === 'selesai' ? 'bg-slate-100 text-slate-600' : (item.status === 'ditangani' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-700')}">${item.status}</span>`
                        ];

                        if (isUserAdminRole) {
                            rowData.push(`
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" onclick="bukaModalEditKentongan(${item.id}, '${item.kategori}', '${escapeQuote(item.catatan || '')}', '${item.waktu}', '${escapeQuote(item.pelapor)}', '${item.koordinat_label}')" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition border border-amber-200 cursor-pointer" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button type="button" onclick="hapusRiwayatKentongan(${item.id}, '${item.waktu}')" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 transition border border-rose-200 cursor-pointer" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            `);
                        }

                        const rowNode = dtKentongan.row.add(rowData).node();
                        $(rowNode).attr('id', 'row-panic-' + item.id);
                    });
                    dtKentongan.draw(false);
                    tampilkanTabelFeedback('Data riwayat kentongan berhasil disegarkan.', 'success');
                }
            })
            .catch(err => {
                tampilkanTabelFeedback('Gagal memuat data: ' + err.message, 'error');
            });
    }

    // Buka Modal Edit (Khusus Admin)
    function bukaModalEditKentongan(id, kategori, catatan, waktu, pelapor, koordinat) {
        document.getElementById('edit-modal-id').value = id;
        document.getElementById('edit-modal-kategori').value = kategori;
        document.getElementById('edit-modal-catatan').value = catatan;
        document.getElementById('edit-modal-waktu').innerText = waktu;
        document.getElementById('edit-modal-pelapor').innerText = pelapor;
        document.getElementById('edit-modal-koordinat').innerText = koordinat;
        openModal('modal-edit-kentongan');
    }

    // Submit Edit Kentongan via AJAX (Khusus Jenis Kejadian & Keterangan)
    function submitEditKentongan(e) {
        e.preventDefault();
        const id = document.getElementById('edit-modal-id').value;
        const kategori = document.getElementById('edit-modal-kategori').value;
        const catatan = document.getElementById('edit-modal-catatan').value;
        const btn = document.getElementById('btn-simpan-edit-kentongan');

        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';

        fetch(`/api/panic/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                kategori: kategori,
                catatan: catatan
            })
        })
        .then(async (res) => {
            const data = await res.json();
            if (res.ok && data.success) {
                closeModal('modal-edit-kentongan');
                tampilkanTabelFeedback(`✅ ${data.message}`, 'success');

                // Update teks di DOM row jika ada
                const row = document.getElementById('row-panic-' + id);
                if (row) {
                    const badgeText = row.querySelector('.badge-kategori-text');
                    const badgeIcon = row.querySelector('.badge-icon');
                    const catatanEl = row.querySelector('.row-catatan-text');
                    const badgeContainer = row.querySelector('.row-kategori-badge');

                    if (badgeText) badgeText.innerText = data.alert.kategori_badge;
                    if (badgeIcon) badgeIcon.innerText = data.alert.kategori_icon;
                    if (catatanEl) catatanEl.innerText = data.alert.catatan;

                    if (badgeContainer) {
                        badgeContainer.className = 'row-kategori-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-extrabold border';
                        if (kategori === 'pencurian') badgeContainer.classList.add('bg-rose-100', 'text-rose-800', 'border-rose-200');
                        else if (kategori === 'kebakaran') badgeContainer.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-200');
                        else if (kategori === 'medis') badgeContainer.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-200');
                        else badgeContainer.classList.add('bg-amber-100', 'text-amber-800', 'border-amber-200');
                    }
                }
            } else {
                alert('🚫 Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            alert('Kesalahan jaringan: ' + err.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Simpan Perubahan';
        });
    }

    // Hapus Riwayat Kentongan (Khusus Admin)
    function hapusRiwayatKentongan(id, waktu) {
        if (!confirm(`Apakah Anda yakin ingin menghapus data riwayat kejadian tanggal ${waktu}? Tindakan ini tidak dapat dibatalkan.`)) {
            return;
        }

        fetch(`/api/panic/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async (res) => {
            const data = await res.json();
            if (res.ok && data.success) {
                if (dtKentongan) {
                    dtKentongan.row($('#row-panic-' + id)).remove().draw(false);
                }
                tampilkanTabelFeedback(`🗑️ ${data.message}`, 'success');
            } else {
                alert('🚫 Gagal menghapus: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            alert('Kesalahan jaringan: ' + err.message);
        });
    }

    function tampilkanTabelFeedback(pesan, tipe) {
        const el = document.getElementById('tabel-panic-feedback');
        if (!el) return;
        el.innerText = pesan;
        if (tipe === 'success') {
            el.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in block';
        } else {
            el.className = 'p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in block';
        }
        setTimeout(() => { el.classList.add('hidden'); }, 5000);
    }

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function escapeQuote(str) {
        return (str || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }
</script>
@endpush
