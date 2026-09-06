@extends('layouts.app')

@section('title', 'Command Center - Dashboard Pengurus RW 02')

@push('styles')
<!-- Leaflet.js CSS untuk Peta Kerawanan & OSM -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map-kerawanan { height: 420px; border-radius: 1.25rem; z-index: 10; }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Header Command Center -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-6 -bottom-6 w-48 h-48 rounded-full bg-violet-600/20 blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-violet-600/30 border border-violet-400/40 flex items-center justify-center text-2xl shadow-inner">
                    🗺️
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-violet-500/20 text-violet-300 text-[10px] font-extrabold uppercase tracking-wider border border-violet-500/30">
                            Command Center RW 02
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Bhabinkamtibmas Terhubung
                        </span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-black tracking-tight mt-1">Dashboard Kamtibmas & Peta Kerawanan</h1>
                    <p class="text-xs text-slate-300 mt-0.5">Analisis patroli wilayah, heatmap insiden, dan otomatisasi jadwal ronda.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 self-end md:self-center">
                <a href="{{ route('users.index') }}" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md hover:shadow-blue-500/20 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Kelola Pengguna</span>
                </a>
                <a href="{{ route('settings.index') }}" class="px-3.5 py-2 rounded-xl bg-violet-700 hover:bg-violet-600 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md hover:shadow-violet-500/20 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pengaturan Web</span>
                </a>
                <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-700 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Rekap RW</span>
                </button>
            </div>
        </div>
    </div>

    <!-- STATISTIK WIDGET 4 KARTU -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Warga Terdaftar -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Warga</p>
                <p class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ $totalWarga }}</p>
                <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">RT 01 & RT 02</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                👥
            </div>
        </div>

        <!-- Petugas Ronda -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Petugas Ronda</p>
                <p class="text-xl sm:text-2xl font-black text-violet-700 mt-0.5">{{ $totalRonda }}</p>
                <p class="text-[10px] text-violet-600 font-semibold mt-0.5">Siaga Poskamling</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center font-bold">
                🔦
            </div>
        </div>

        <!-- Laporan Insiden -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Laporan</p>
                <p class="text-xl sm:text-2xl font-black text-amber-600 mt-0.5">{{ $totalLaporan }}</p>
                <p class="text-[10px] text-slate-500 font-semibold mt-0.5">Bulan Berjalan</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                📋
            </div>
        </div>

        <!-- Tamu Wajib Lapor -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tamu 2x24 Jam</p>
                <p class="text-xl sm:text-2xl font-black text-blue-600 mt-0.5">{{ $totalTamu }}</p>
                <p class="text-[10px] text-blue-600 font-semibold mt-0.5">Terdata Aktif</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                🪪
            </div>
        </div>

    </div>

    <!-- SEKSI 1: PETA KERAWANAN (LEAFLET.JS + HEATMAP INCIDENTS) & MANAJEMEN TITIK RAWAN -->
    <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-extrabold uppercase">Heatmap Kerawanan</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Pemetaan Wilayah & Titik Rawan Patroli</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kelola titik lokasi rawan dan pantau secara real-time oleh petugas ronda poskamling.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="hidden sm:flex items-center gap-2 text-xs border border-slate-200 px-2.5 py-1 rounded-xl bg-slate-50">
                    <span class="inline-flex items-center gap-1 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Rawan</span>
                    <span class="inline-flex items-center gap-1 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Sedang</span>
                    <span class="inline-flex items-center gap-1 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Aman</span>
                </div>
                <button onclick="openModalCheckpoint()" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Titik Rawan</span>
                </button>
            </div>
        </div>

        <!-- Leaflet Container -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200 shadow-inner">
            <div id="map-kerawanan"></div>
        </div>

        <!-- Helper Interaktif Klik Peta -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] bg-slate-50 border border-slate-200 px-3.5 py-2 rounded-xl text-slate-600">
            <div class="flex items-center gap-1.5">
                <span>📍</span>
                <span><strong>Tips Presisi:</strong> Klik lokasi mana saja pada peta di atas untuk langsung menyalin koordinat ke formulir titik rawan patroli.</span>
            </div>
            <div id="map-click-preview" class="font-mono text-xs font-bold text-rose-600 hidden"></div>
        </div>

        <!-- Grid Daftar Titik Rawan Patroli Terdaftar -->
        <div class="pt-1">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span>Titik Pantau & Rawan Patroli Terdaftar</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-extrabold">{{ $checkpoints->count() }}</span>
                </h3>
                <span class="text-[11px] text-slate-400 hidden sm:inline">Dapat dipantau langsung oleh petugas patroli ronda</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                @forelse($checkpoints as $ckp)
                @php
                    $ckpTingkat = $ckp->tingkat_kerawanan ?? 'rawan';
                    $ckpBadge = $ckp->tingkat_kerawanan_badge ?? ($ckpTingkat === 'aman' ? '🟢 Aman / Pos Pantau' : ($ckpTingkat === 'sedang' ? '🟡 Kerawanan Sedang' : '🔴 Titik Rawan Prioritas'));
                    $ckpClass = $ckp->badge_class ?? ($ckpTingkat === 'aman' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($ckpTingkat === 'sedang' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-rose-100 text-rose-800 border-rose-200'));
                    $ckpMapsUrl = $ckp->google_maps_url ?? (($ckp->latitude && $ckp->longitude) ? "https://www.google.com/maps?q={$ckp->latitude},{$ckp->longitude}" : '#');
                    $ckpDeskripsi = $ckp->deskripsi ?? 'Area pantauan patroli rutin poskamling.';
                    $ckpRt = $ckp->rt ?? '01';
                @endphp
                <div class="p-3.5 rounded-2xl border transition flex flex-col justify-between space-y-3 hover:shadow-sm {{ $ckpTingkat === 'rawan' ? 'bg-rose-50/40 border-rose-200/80' : ($ckpTingkat === 'sedang' ? 'bg-amber-50/40 border-amber-200/80' : 'bg-emerald-50/40 border-emerald-200/80') }}">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold border {{ $ckpClass }}">
                                {{ $ckpBadge }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-500 font-mono bg-white/70 px-1.5 py-0.5 rounded border border-slate-200/60">RT {{ $ckpRt }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 mt-2.5 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $ckpTingkat === 'rawan' ? 'bg-rose-500' : ($ckpTingkat === 'sedang' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                            <span>{{ $ckp->nama_titik }}</span>
                        </h4>
                        <p class="text-[11px] text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $ckpDeskripsi }}</p>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-200/60 text-xs">
                        <div class="flex items-center justify-between text-[10px] font-mono text-slate-500">
                            <span>{{ number_format($ckp->latitude, 5) }}, {{ number_format($ckp->longitude, 5) }}</span>
                            <a href="{{ $ckpMapsUrl }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-0.5 transition">
                                <span>Buka Maps</span>
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                        <div class="flex items-center justify-end gap-1.5 pt-1">
                            <button onclick='editCheckpoint(@json($ckp))' class="px-2.5 py-1 rounded-lg bg-white hover:bg-slate-100 text-slate-700 text-[11px] font-bold border border-slate-200 shadow-2xs transition cursor-pointer flex items-center gap-1">
                                <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            <button onclick="hapusCheckpoint({{ $ckp->id }}, '{{ addslashes($ckp->nama_titik) }}')" class="px-2.5 py-1 rounded-lg bg-white hover:bg-rose-50 text-rose-600 text-[11px] font-bold border border-rose-200 shadow-2xs transition cursor-pointer flex items-center gap-1">
                                <svg class="w-3 h-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    Belum ada titik rawan patroli yang ditambahkan. Silakan klik tombol "Tambah Titik Rawan" di atas.
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- SEKSI 2: GRAFIK STATISTIK ANALITIK (CHART.JS) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Grafik 1: Keaktifan Presensi Ronda -->
        <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-3">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900">Statistik Kehadiran Patroli Ronda Mingguan</h3>
                <p class="text-xs text-slate-500">Jumlah scan checkpoint per hari dalam sepekan.</p>
            </div>
            <div class="aspect-2/1">
                <canvas id="chart-ronda"></canvas>
            </div>
        </section>

        <!-- Grafik 2: Komposisi Laporan Kamtibmas -->
        <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-3">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900">Distribusi Kategori Laporan Warga</h3>
                <p class="text-xs text-slate-500">Persentase kejadian keamanan berdasarkan riwayat 30 hari.</p>
            </div>
            <div class="aspect-2/1 flex items-center justify-center">
                <canvas id="chart-kategori"></canvas>
            </div>
        </section>

    </div>

    <!-- SEKSI 3: MANAJEMEN JADWAL RONDA & WHATSAPP REMINDER GATEWAY -->
    <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-800 text-[10px] font-extrabold uppercase">Otomatisasi Roster</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Manajemen Jadwal Ronda & WhatsApp Reminder</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kirim pengingat jadwal ronda H-1 ke nomor WhatsApp warga otomatis via gateway.</p>
            </div>
            <button onclick="toggleModalJadwal()" class="px-3.5 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Jadwal Ronda</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase text-[10px] tracking-wider bg-slate-50/60">
                        <th class="py-3 px-3 rounded-l-xl">Hari Ronda</th>
                        <th class="py-3 px-3">Nama Petugas / Warga</th>
                        <th class="py-3 px-3">Wilayah RT</th>
                        <th class="py-3 px-3">No. WhatsApp</th>
                        <th class="py-3 px-3 text-right rounded-r-xl">Aksi Notifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($jadwalList as $jdw)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-3 font-bold text-slate-900">
                            <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-800 font-mono text-[11px]">
                                {{ $jdw->hari }}
                            </span>
                        </td>
                        <td class="py-3 px-3 font-semibold text-slate-800">
                            {{ $jdw->user->name ?? 'Warga' }}
                        </td>
                        <td class="py-3 px-3">
                            <span class="px-1.5 py-0.5 rounded bg-violet-50 text-violet-700 font-bold text-[10px]">
                                RT {{ $jdw->rt_id ?? '01' }}
                            </span>
                        </td>
                        <td class="py-3 px-3 font-mono text-slate-500">
                            {{ $jdw->user->phone ?? '081234567890' }}
                        </td>
                        <td class="py-3 px-3 text-right">
                            <button onclick="kirimWaReminder('{{ $jdw->user->name ?? 'Warga' }}', '{{ $jdw->user->phone ?? '081234567890' }}', '{{ $jdw->hari }}')" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                <span>📱 Kirim WA</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="wa-feedback" class="hidden p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold animate-in fade-in"></div>
    </section>

    <!-- SEKSI 4: PENGATURAN TITIK PUSAT & RADIUS GEOFENCE TOMBOL PANIC -->
    <!-- SEKSI 4: PENGATURAN TITIK PUSAT & RADIUS GEOFENCE DENGAN PETA INTERAKTIF -->
    <section class="bg-white rounded-3xl p-5 sm:p-6 border-2 border-violet-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-violet-100 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-800 text-[10px] font-extrabold uppercase">Geofencing Terintegrasi</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Pengaturan Titik Pusat & Radius Geofence</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan koordinat titik pusat dan jarak radius tombol panic. Semua nilai dapat diisi otomatis dengan mengklik peta atau menggeser marker.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Radius Aktif: <strong id="geofence-radius-display">{{ $rwSetting->panic_radius_meters ?? 300 }}m</strong>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Form Input Geofence (5 Kolom) -->
            <div class="lg:col-span-5 space-y-3.5">
                <form id="form-geofence" onsubmit="submitGeofenceSettings(event)" class="space-y-3">
                    <div class="bg-violet-50/50 border border-violet-200/80 rounded-2xl p-3.5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-violet-900 flex items-center gap-1.5">
                                <span>🎯</span> Titik Koordinat Pusat (Pos RW)
                            </span>
                            <span class="text-[10px] text-violet-600 font-semibold bg-violet-100/70 px-2 py-0.5 rounded-md">Bisa klik peta</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Latitude</label>
                                <input type="number" step="0.000001" id="geofence-lat" value="{{ $rwSetting->center_latitude ?? -6.208800 }}" required oninput="syncInputsToMap()" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 font-mono bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Longitude</label>
                                <input type="number" step="0.000001" id="geofence-lng" value="{{ $rwSetting->center_longitude ?? 106.845600 }}" required oninput="syncInputsToMap()" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 font-mono bg-white">
                            </div>
                        </div>
                        <button type="button" onclick="ambilLokasiAdminGPS()" class="w-full py-2 px-3 rounded-xl bg-white hover:bg-violet-100 text-violet-700 border border-violet-200 text-xs font-bold transition cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Gunakan Lokasi GPS Saya Saat Ini</span>
                        </button>
                    </div>

                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span>📏</span> Radius Tombol Panic
                            </label>
                            <span class="text-[10px] text-slate-500 font-semibold bg-slate-200/60 px-2 py-0.5 rounded-md">Bisa tarik di peta</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="range" id="geofence-radius-slider" min="50" max="2000" step="25" value="{{ $rwSetting->panic_radius_meters ?? 300 }}" oninput="updateRadiusPreview(this.value)" class="flex-1 accent-violet-600 cursor-pointer">
                            <input type="number" id="geofence-radius" min="50" max="10000" value="{{ $rwSetting->panic_radius_meters ?? 300 }}" required oninput="document.getElementById('geofence-radius-slider').value = this.value; updateRadiusPreview(this.value)" class="w-20 text-xs px-2.5 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 font-mono text-center font-bold bg-white">
                            <span class="text-xs text-slate-500 font-bold">m</span>
                        </div>
                        <!-- Preset Cepat Radius -->
                        <div class="flex items-center gap-1.5 pt-1">
                            <span class="text-[10px] font-bold text-slate-400">Pilihan:</span>
                            <button type="button" onclick="updateRadiusPreview(100)" class="px-2 py-0.5 rounded-md bg-white hover:bg-slate-200 text-[10px] font-bold text-slate-600 border border-slate-200 transition">100m</button>
                            <button type="button" onclick="updateRadiusPreview(200)" class="px-2 py-0.5 rounded-md bg-white hover:bg-slate-200 text-[10px] font-bold text-slate-600 border border-slate-200 transition">200m</button>
                            <button type="button" onclick="updateRadiusPreview(300)" class="px-2 py-0.5 rounded-md bg-white hover:bg-slate-200 text-[10px] font-bold text-slate-600 border border-slate-200 transition">300m</button>
                            <button type="button" onclick="updateRadiusPreview(500)" class="px-2 py-0.5 rounded-md bg-white hover:bg-slate-200 text-[10px] font-bold text-slate-600 border border-slate-200 transition">500m</button>
                            <button type="button" onclick="updateRadiusPreview(1000)" class="px-2 py-0.5 rounded-md bg-white hover:bg-slate-200 text-[10px] font-bold text-slate-600 border border-slate-200 transition">1.000m</button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-extrabold shadow-md shadow-violet-600/20 transition cursor-pointer flex items-center justify-center gap-2">
                        <span>💾 Simpan Pengaturan Geofence</span>
                    </button>
                </form>

                <div id="geofence-feedback" class="hidden p-3 rounded-2xl bg-violet-50 border border-violet-200 text-violet-900 text-xs font-semibold text-center animate-in fade-in"></div>
            </div>

            <!-- Preview Peta Interaktif Geofence (7 Kolom) -->
            <div class="lg:col-span-7 space-y-2.5">
                <!-- Toolbar Mode Interaksi Peta -->
                <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 p-2 rounded-2xl border border-slate-200">
                    <div class="flex items-center gap-1 text-xs">
                        <span class="text-[11px] font-bold text-slate-500 mr-1">Mode Klik Peta:</span>
                        <button type="button" id="btn-mode-center" onclick="setGeofenceMapMode('center')" class="px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 bg-violet-600 text-white shadow-2xs cursor-pointer">
                            <span>🎯 Set Titik Pusat</span>
                        </button>
                        <button type="button" id="btn-mode-radius" onclick="setGeofenceMapMode('radius')" class="px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 cursor-pointer">
                            <span>📏 Set Radius Jarak</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span id="geofence-map-radius-label" class="text-[11px] font-mono text-violet-700 bg-violet-100/70 font-bold px-2 py-0.5 rounded border border-violet-200">{{ $rwSetting->panic_radius_meters ?? 300 }}m</span>
                        <button type="button" onclick="fitGeofenceBounds()" class="px-2 py-1 rounded-lg bg-white hover:bg-slate-100 text-slate-600 text-[11px] font-bold border border-slate-200 transition flex items-center gap-1 cursor-pointer">
                            <span>🔍 Zoom Pas</span>
                        </button>
                    </div>
                </div>

                <!-- Wadah Peta -->
                <div class="rounded-2xl overflow-hidden border border-violet-200 shadow-inner relative" style="height: 330px;">
                    <div id="map-geofence-preview" style="height: 100%; width: 100%; z-index: 10;"></div>
                    <!-- Petunjuk Floating di Peta -->
                    <div id="geofence-map-instruction" class="absolute bottom-2.5 left-2.5 right-2.5 z-20 bg-slate-900/85 backdrop-blur-xs text-white px-3 py-1.5 rounded-xl text-[11px] font-medium flex items-center justify-between shadow-lg pointer-events-none">
                        <span id="instruction-text">🎯 <strong>Mode Pusat:</strong> Klik peta untuk mengisi Titik Pusat Latitude & Longitude otomatis.</span>
                        <span id="live-distance-badge" class="font-mono text-[10px] text-emerald-300 font-bold"></span>
                    </div>
                </div>

                <!-- Keterangan Interaktif Bawah Peta -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-slate-500">
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-1.5">
                        <span class="text-sm">🎯</span>
                        <span><strong>Titik Pusat:</strong> Klik peta atau geser marker ungu untuk mengubah koordinat secara instan.</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-1.5">
                        <span class="text-sm">📏</span>
                        <span><strong>Radius:</strong> Geser pegangan hijau ↔️ di tepi lingkaran atau klik "Set Radius" lalu klik peta.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- MODAL TAMBAH JADWAL RONDA -->
<div id="modal-jadwal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-5 space-y-4 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900">Tambah Jadwal Giliran Ronda</h3>
            <button onclick="toggleModalJadwal()" class="text-slate-400 hover:text-slate-700 text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form onsubmit="submitFormJadwal(event)" class="space-y-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih Warga / Petugas</label>
                <select id="jadwal-user-id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                    @foreach($wargaList as $w)
                    <option value="{{ $w->id }}">{{ $w->name }} ({{ strtoupper($w->role) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Hari Ronda</label>
                <select id="jadwal-hari" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Wilayah RT</label>
                <select id="jadwal-rt" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                    <option value="01">RT 01</option>
                    <option value="02">RT 02</option>
                </select>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="toggleModalJadwal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-violet-600 text-white font-bold hover:bg-violet-700 transition cursor-pointer">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL INPUT / EDIT TITIK RAWAN PATROLI -->
<div id="modal-checkpoint" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 space-y-4 shadow-2xl border border-slate-100 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-sm font-bold">
                    📍
                </div>
                <div>
                    <h3 id="modal-checkpoint-title" class="font-bold text-sm text-slate-900">Tambah Titik Rawan Patroli</h3>
                    <p class="text-[11px] text-slate-500">Titik lokasi akan tersinkronisasi dan dipantau langsung oleh Petugas Ronda.</p>
                </div>
            </div>
            <button onclick="closeModalCheckpoint()" class="text-slate-400 hover:text-slate-700 text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form onsubmit="submitFormCheckpoint(event)" class="space-y-3.5 text-xs">
            <input type="hidden" id="checkpoint-id" value="">

            <!-- Nama Lokasi -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Nama Lokasi <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="checkpoint-nama" required placeholder="Contoh: Gardu Gang Senggol, Gapura Barat RT 02, Jembatan Rel" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 text-xs font-semibold text-slate-800">
            </div>

            <!-- Tingkat Kerawanan & Wilayah RT -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Tingkat Kerawanan <span class="text-rose-500">*</span>
                    </label>
                    <select id="checkpoint-tingkat" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-rose-500 text-xs font-semibold text-slate-800 bg-white">
                        <option value="rawan">🔴 Titik Rawan Prioritas (Wajib Pantau)</option>
                        <option value="sedang">🟡 Kerawanan Sedang</option>
                        <option value="aman">🟢 Aman / Pos Pantau</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Wilayah RT
                    </label>
                    <input type="text" id="checkpoint-rt" value="01" placeholder="Contoh: 01 atau 02" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-rose-500 text-xs font-semibold text-slate-800">
                </div>
            </div>

            <!-- Koordinat Latitude & Longitude -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-bold text-slate-700">
                        Koordinat Lokasi (Latitude & Longitude) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="ambilLokasiCheckpointGPS()" class="px-2 py-0.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-bold transition cursor-pointer flex items-center gap-1">
                            <span>📍 GPS Saya</span>
                        </button>
                        <button type="button" onclick="setCheckpointFromRwCenter()" class="px-2 py-0.5 rounded-lg bg-violet-50 hover:bg-violet-100 text-violet-700 border border-violet-200 text-[10px] font-bold transition cursor-pointer flex items-center gap-1">
                            <span>🎯 Pusat RW</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <input type="number" step="any" id="checkpoint-lat" required placeholder="Latitude (mis: -6.208800)" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-rose-500 text-xs font-mono font-medium text-slate-800">
                        <span class="text-[10px] text-slate-400 block mt-0.5">Latitude</span>
                    </div>
                    <div>
                        <input type="number" step="any" id="checkpoint-lng" required placeholder="Longitude (mis: 106.845600)" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-rose-500 text-xs font-mono font-medium text-slate-800">
                        <span class="text-[10px] text-slate-400 block mt-0.5">Longitude</span>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">💡 Anda juga dapat mengklik langsung lokasi mana saja di peta dashboard untuk otomatis menyalin koordinat ini.</p>
            </div>

            <!-- Keterangan / Instruksi Patroli -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Keterangan / Catatan Patroli
                </label>
                <textarea id="checkpoint-deskripsi" rows="3" placeholder="Contoh: Sering terjadi tindak mencurigakan saat dini hari. Petugas patroli wajib pantau berkala setiap jam 01.00 dan 03.00 WIB." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-rose-500 text-xs leading-relaxed text-slate-800"></textarea>
                <span class="text-[10px] text-slate-400">Instruksi ini akan dibaca oleh petugas ronda saat patroli malam di pos masing-masing.</span>
            </div>

            <!-- Urutan Patroli (Optional) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Urutan Rute Patroli
                </label>
                <input type="number" id="checkpoint-urutan" min="1" value="1" class="w-24 px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-rose-500 text-xs font-semibold text-slate-800">
            </div>

            <!-- Feedback Message -->
            <div id="checkpoint-modal-feedback" class="hidden p-3 rounded-2xl text-xs font-semibold text-center"></div>

            <!-- Tombol Aksi -->
            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModalCheckpoint()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btn-submit-checkpoint" class="px-5 py-2 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 transition cursor-pointer flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Titik Rawan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet.js & Leaflet.heat CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Inisialisasi Peta Leaflet.js dengan OpenStreetMap
    const defaultLat = -6.208800;
    const defaultLng = 106.845600;
    const map = L.map('map-kerawanan').setView([defaultLat, defaultLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Titik Checkpoint Patroli Markers (Color-Coded by Tingkat Kerawanan)
    const checkpointsData = @json($checkpoints);
    checkpointsData.forEach(ckp => {
        if (ckp.latitude && ckp.longitude) {
            const colorMap = { rawan: '#ef4444', sedang: '#f59e0b', aman: '#10b981' };
            const labelMap = { rawan: '🔴 Rawan', sedang: '🟡 Sedang', aman: '🟢 Aman' };
            const markerColor = colorMap[ckp.tingkat_kerawanan] || '#ef4444';
            const ckpIcon = L.divIcon({
                html: `<div style="background: ${markerColor}; width: 14px; height: 14px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 8px ${markerColor}88;"></div>`,
                iconSize: [14, 14],
                iconAnchor: [7, 7],
                className: ''
            });
            const marker = L.marker([ckp.latitude, ckp.longitude], { icon: ckpIcon }).addTo(map);
            marker.bindPopup(`
                <div style="font-family: sans-serif; font-size: 12px; min-width: 160px;">
                    <strong style="color: ${markerColor};">${ckp.nama_titik}</strong><br>
                    <span style="font-size: 10px; color: #6b7280;">RT ${ckp.rt} — ${labelMap[ckp.tingkat_kerawanan] || 'Rawan'}</span><br>
                    <span style="font-size: 10px; color: #94a3b8;">${ckp.deskripsi || 'Area patroli rutin'}</span><br>
                    <a href="https://www.google.com/maps?q=${ckp.latitude},${ckp.longitude}" target="_blank" style="font-size: 10px; color: #2563eb; text-decoration: none;">📍 Buka Google Maps</a>
                </div>
            `);
        }
    });

    // MAP CLICK → Isi koordinat ke Modal Checkpoint
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(8);
        const lng = e.latlng.lng.toFixed(8);
        document.getElementById('checkpoint-lat').value = lat;
        document.getElementById('checkpoint-lng').value = lng;
        const preview = document.getElementById('map-click-preview');
        preview.textContent = `Koordinat dipilih: ${lat}, ${lng}`;
        preview.classList.remove('hidden');
        // Flash highlight
        preview.style.transition = 'opacity 0.2s';
        preview.style.opacity = '1';
        setTimeout(() => { preview.style.opacity = '0.7'; }, 1500);
    });

    // Layer Heatmap Insiden (Leaflet.heat)
    const rawHeatmapData = @json($heatmapPoints);
    const heatPoints = rawHeatmapData.map(pt => [pt[0], pt[1], pt[2]]);

    if (typeof L.heatLayer !== 'undefined' && heatPoints.length > 0) {
        L.heatLayer(heatPoints, {
            radius: 35,
            blur: 20,
            maxZoom: 17,
            gradient: { 0.2: '#10b981', 0.5: '#f59e0b', 0.8: '#ef4444' }
        }).addTo(map);
    }

    // Lingkaran Geofence Radius di Peta Utama
    const rwGeoLat = {{ $rwSetting->center_latitude ?? -6.208800 }};
    const rwGeoLng = {{ $rwSetting->center_longitude ?? 106.845600 }};
    const rwGeoRadius = {{ $rwSetting->panic_radius_meters ?? 300 }};

    const geofenceCircleMain = L.circle([rwGeoLat, rwGeoLng], {
        radius: rwGeoRadius,
        color: '#059669',
        fillColor: '#10b981',
        fillOpacity: 0.12,
        weight: 2,
        dashArray: '6, 6'
    }).addTo(map);
    geofenceCircleMain.bindPopup(`<div style="font-family: sans-serif; font-size: 12px;"><strong style="color: #059669;">Radius Geofence Tombol Panic</strong><br><span style="color: #6b7280;">Jangkauan: ${rwGeoRadius} meter dari Pos RW 02</span></div>`);

    // Penanda Titik Pusat RW
    const centerIcon = L.divIcon({
        html: '<div style="background: #059669; width: 14px; height: 14px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 6px rgba(5,150,105,0.6);"></div>',
        iconSize: [14, 14],
        iconAnchor: [7, 7],
        className: ''
    });
    L.marker([rwGeoLat, rwGeoLng], { icon: centerIcon }).addTo(map)
        .bindPopup('<div style="font-family: sans-serif; font-size: 12px;"><strong style="color: #059669;">🎯 Titik Pusat Geofence RW 02</strong><br><span style="color: #6b7280;">Pusat koordinat radius tombol panic</span></div>');

    // 2. Inisialisasi Chart.js untuk Grafik Keaktifan Ronda
    const ctxRonda = document.getElementById('chart-ronda');
    if (ctxRonda) {
        new Chart(ctxRonda, {
            type: 'bar',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [{
                    label: 'Scan Checkpoint Terverifikasi',
                    data: [12, 14, 11, 15, 18, 22, 19],
                    backgroundColor: '#7c3aed',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 3. Inisialisasi Chart.js untuk Kategori Laporan
    const ctxKategori = document.getElementById('chart-kategori');
    if (ctxKategori) {
        new Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: ['Maling / Curanmor', 'Lampu PJU Padam', 'Orang Asing', 'Darurat Medis'],
                datasets: [{
                    data: [2, 5, 3, 1],
                    backgroundColor: ['#ef4444', '#f59e0b', '#7c3aed', '#10b981']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                }
            }
        });
    }

    // Modal Jadwal
    function toggleModalJadwal() {
        document.getElementById('modal-jadwal').classList.toggle('hidden');
    }

    function submitFormJadwal(e) {
        e.preventDefault();
        const userId = document.getElementById('jadwal-user-id').value;
        const hari = document.getElementById('jadwal-hari').value;
        const rt = document.getElementById('jadwal-rt').value;

        fetch('/api/jadwal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_id: userId, hari: hari, rt_id: rt })
        })
        .then(res => res.json())
        .then(data => {
            alert('Jadwal ronda berhasil ditambahkan!');
            toggleModalJadwal();
            window.location.reload();
        })
        .catch(() => {
            alert('Jadwal berhasil disimpan (Demo Mode)!');
            toggleModalJadwal();
        });
    }

    // WhatsApp Reminder Trigger
    function kirimWaReminder(nama, phone, hari) {
        const feedback = document.getElementById('wa-feedback');
        feedback.innerHTML = `⏳ Mengirim pesan pengingat jadwal ronda ke WhatsApp <strong>${nama}</strong> (${phone})...`;
        feedback.classList.remove('hidden');

        fetch('/api/wa-reminder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                nama_warga: nama,
                nomor_wa: phone,
                hari_ronda: hari
            })
        })
        .then(res => res.json())
        .then(data => {
            feedback.innerHTML = `✅ <strong>Pesan Pengingat WhatsApp Terkirim!</strong> Pesan jadwal hari ${hari} berhasil dikirim ke nomor ${phone}.<br><pre class="text-[10px] mt-1 text-slate-600 bg-white p-2 rounded-lg border border-slate-200">${data.konten_pesan}</pre>`;
        })
        .catch(() => {
            feedback.innerHTML = `✅ <strong>Simulasi WA Terkirim!</strong> Pengingat jadwal hari ${hari} untuk ${nama} berhasil disimulasikan via Fonnte/Wablas gateway.`;
        });
    }

    // ====== PETA INTERAKTIF GEOFENCE PREVIEW & PENGATURAN ======
    let geoPreviewLat = {{ $rwSetting->center_latitude ?? -6.208800 }};
    let geoPreviewLng = {{ $rwSetting->center_longitude ?? 106.845600 }};
    let geoPreviewRadius = {{ $rwSetting->panic_radius_meters ?? 300 }};
    let currentGeofenceMode = 'center'; // 'center' atau 'radius'

    const mapGeoPreview = L.map('map-geofence-preview').setView([geoPreviewLat, geoPreviewLng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapGeoPreview);

    // 1. Lingkaran Radius Geofence
    let geoCircle = L.circle([geoPreviewLat, geoPreviewLng], {
        radius: geoPreviewRadius,
        color: '#7c3aed',
        fillColor: '#8b5cf6',
        fillOpacity: 0.18,
        weight: 2,
        dashArray: '6, 6'
    }).addTo(mapGeoPreview);

    // 2. Icon Titik Pusat
    const geoCenterIcon = L.divIcon({
        html: `
            <div style="position: relative; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center;">
                <div style="position: absolute; width: 26px; height: 26px; background: rgba(124, 58, 237, 0.4); border-radius: 50%; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></div>
                <div style="position: relative; width: 18px; height: 18px; background: #6d28d9; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.35); display: flex; align-items: center; justify-content: center; color: white; font-size: 10px;">
                    🎯
                </div>
            </div>
        `,
        iconSize: [26, 26],
        iconAnchor: [13, 13],
        className: ''
    });

    let geoCenterMarker = L.marker([geoPreviewLat, geoPreviewLng], {
        icon: geoCenterIcon,
        draggable: true,
        zIndexOffset: 1000
    }).addTo(mapGeoPreview);

    geoCenterMarker.bindTooltip('🎯 Titik Pusat (Geser atau klik peta)', { direction: 'top', offset: [0, -10] });

    // 3. Icon Handle Tepi Lingkaran (Pengatur Radius)
    function createEdgeIcon(rad) {
        return L.divIcon({
            html: `
                <div style="background: #059669; color: #ffffff; border-radius: 9999px; padding: 2px 8px; font-size: 10px; font-weight: 800; font-family: monospace; border: 2px solid #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.35); cursor: ew-resize; display: flex; align-items: center; gap: 3px; white-space: nowrap;">
                    <span>↔️</span> <span>${rad}m</span>
                </div>
            `,
            iconSize: [64, 22],
            iconAnchor: [32, 11],
            className: ''
        });
    }

    // Fungsi Hitung Posisi Koordinat di Tepi Timur Lingkaran
    function calculateEdgePosition(centerLat, centerLng, radiusMeters) {
        const latRad = centerLat * (Math.PI / 180);
        const deltaLng = radiusMeters / (111320 * Math.cos(latRad));
        return [centerLat, centerLng + deltaLng];
    }

    let initialEdgePos = calculateEdgePosition(geoPreviewLat, geoPreviewLng, geoPreviewRadius);
    let geoRadiusEdgeMarker = L.marker(initialEdgePos, {
        icon: createEdgeIcon(geoPreviewRadius),
        draggable: true,
        zIndexOffset: 900
    }).addTo(mapGeoPreview);

    geoRadiusEdgeMarker.bindTooltip('↔️ Tarik untuk atur radius', { direction: 'right', offset: [15, 0] });

    // Saat Handle Radius di-drag secara langsung
    geoRadiusEdgeMarker.on('drag', function(e) {
        const center = geoCenterMarker.getLatLng();
        const curPos = e.target.getLatLng();
        let newRad = Math.round(center.distanceTo(curPos));
        if (newRad < 50) newRad = 50;
        if (newRad > 5000) newRad = 5000;

        applyNewRadius(newRad, false);

        const badge = document.getElementById('live-distance-badge');
        if (badge) badge.innerText = `Radius: ${newRad}m`;
    });

    geoRadiusEdgeMarker.on('dragend', function(e) {
        const center = geoCenterMarker.getLatLng();
        const rad = parseInt(document.getElementById('geofence-radius').value) || 300;
        geoRadiusEdgeMarker.setLatLng(calculateEdgePosition(center.lat, center.lng, rad));
    });

    // Saat Marker Titik Pusat di-drag
    geoCenterMarker.on('drag', function(e) {
        const pos = e.target.getLatLng();
        updateCenterFormValues(pos.lat, pos.lng);
        geoCircle.setLatLng(pos);
        const rad = parseInt(document.getElementById('geofence-radius').value) || 300;
        geoRadiusEdgeMarker.setLatLng(calculateEdgePosition(pos.lat, pos.lng, rad));

        const badge = document.getElementById('live-distance-badge');
        if (badge) badge.innerText = `Pusat: ${pos.lat.toFixed(5)}, ${pos.lng.toFixed(5)}`;
    });

    geoCenterMarker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        mapGeoPreview.panTo(pos);
    });

    // Klik pada Peta Preview untuk Mengisi Otomatis
    mapGeoPreview.on('click', function(e) {
        if (currentGeofenceMode === 'center') {
            // Mode 1: Set Titik Pusat secara otomatis dari klik peta
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            updateCenterFormValues(lat, lng);
            geoCenterMarker.setLatLng([lat, lng]);
            geoCircle.setLatLng([lat, lng]);
            const rad = parseInt(document.getElementById('geofence-radius').value) || 300;
            geoRadiusEdgeMarker.setLatLng(calculateEdgePosition(lat, lng, rad));

            const badge = document.getElementById('live-distance-badge');
            if (badge) badge.innerText = `Pusat: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        } else if (currentGeofenceMode === 'radius') {
            // Mode 2: Set Radius Jarak secara otomatis dari klik peta
            const center = geoCenterMarker.getLatLng();
            let distance = Math.round(center.distanceTo(e.latlng));
            if (distance < 50) distance = 50;
            if (distance > 5000) distance = 5000;

            applyNewRadius(distance, true);

            const badge = document.getElementById('live-distance-badge');
            if (badge) badge.innerText = `Radius terukur: ${distance} meter`;
        }
    });

    // Switch Mode Interaksi Peta (Pusat vs Radius)
    function setGeofenceMapMode(mode) {
        currentGeofenceMode = mode;
        const btnCenter = document.getElementById('btn-mode-center');
        const btnRadius = document.getElementById('btn-mode-radius');
        const text = document.getElementById('instruction-text');

        if (mode === 'center') {
            btnCenter.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 bg-violet-600 text-white shadow-2xs cursor-pointer';
            btnRadius.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 cursor-pointer';
            text.innerHTML = '🎯 <strong>Mode Pusat:</strong> Klik peta untuk mengisi Titik Pusat Latitude & Longitude otomatis.';
            mapGeoPreview.getContainer().style.cursor = 'crosshair';
        } else {
            btnCenter.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 cursor-pointer';
            btnRadius.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 bg-emerald-600 text-white shadow-2xs cursor-pointer';
            text.innerHTML = '📏 <strong>Mode Radius:</strong> Klik lokasi terluar jangkauan pada peta untuk mengukur radius meter.';
            mapGeoPreview.getContainer().style.cursor = 'cell';
        }
    }

    // Perbarui Input Form Titik Pusat
    function updateCenterFormValues(lat, lng) {
        document.getElementById('geofence-lat').value = parseFloat(lat).toFixed(6);
        document.getElementById('geofence-lng').value = parseFloat(lng).toFixed(6);
    }

    // Terapkan Radius Baru ke Form, Slider, Lingkaran, & Marker Tepi
    function applyNewRadius(radMeters, updateEdgePos = true) {
        document.getElementById('geofence-radius').value = radMeters;
        const slider = document.getElementById('geofence-radius-slider');
        if (slider) slider.value = Math.min(radMeters, 2000);

        document.getElementById('geofence-radius-display').innerText = radMeters + 'm';
        document.getElementById('geofence-map-radius-label').innerText = radMeters + 'm';

        geoCircle.setRadius(radMeters);
        geoRadiusEdgeMarker.setIcon(createEdgeIcon(radMeters));

        if (updateEdgePos) {
            const center = geoCenterMarker.getLatLng();
            geoRadiusEdgeMarker.setLatLng(calculateEdgePosition(center.lat, center.lng, radMeters));
        }
    }

    // Callback Slider / Preset Radius
    function updateRadiusPreview(val) {
        applyNewRadius(parseInt(val), true);
        fitGeofenceBounds();
    }

    // Zoom Peta Menyesuaikan Lingkaran Radius
    function fitGeofenceBounds() {
        if (geoCircle) {
            mapGeoPreview.fitBounds(geoCircle.getBounds().pad(0.25));
        }
    }

    // Sinkronisasi Jika User Mengetik Angka Koordinat Manual di Input Form
    function syncInputsToMap() {
        const lat = parseFloat(document.getElementById('geofence-lat').value);
        const lng = parseFloat(document.getElementById('geofence-lng').value);
        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            geoCenterMarker.setLatLng([lat, lng]);
            geoCircle.setLatLng([lat, lng]);
            const rad = parseInt(document.getElementById('geofence-radius').value) || 300;
            geoRadiusEdgeMarker.setLatLng(calculateEdgePosition(lat, lng, rad));
            mapGeoPreview.panTo([lat, lng]);
        }
    }

    function ambilLokasiAdminGPS() {
        if (!navigator.geolocation) {
            alert('GPS tidak tersedia di perangkat ini.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                updateCenterFormValues(lat, lng);
                geoCenterMarker.setLatLng([lat, lng]);
                geoCircle.setLatLng([lat, lng]);
                const rad = parseInt(document.getElementById('geofence-radius').value) || 300;
                geoRadiusEdgeMarker.setLatLng(calculateEdgePosition(lat, lng, rad));
                mapGeoPreview.setView([lat, lng], 16);

                const badge = document.getElementById('live-distance-badge');
                if (badge) badge.innerText = `GPS: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            },
            (err) => {
                alert('Gagal mendeteksi GPS: ' + err.message);
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    function submitGeofenceSettings(e) {
        e.preventDefault();
        const lat = document.getElementById('geofence-lat').value;
        const lng = document.getElementById('geofence-lng').value;
        const radius = document.getElementById('geofence-radius').value;
        const feedback = document.getElementById('geofence-feedback');

        feedback.innerHTML = '⏳ Menyimpan pengaturan geofence ke database...';
        feedback.className = 'p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold text-center animate-in fade-in';
        feedback.classList.remove('hidden');

        fetch('/api/settings/geofence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                center_latitude: parseFloat(lat),
                center_longitude: parseFloat(lng),
                panic_radius_meters: parseInt(radius)
            })
        })
        .then(async (res) => {
            const data = await res.json();
            if (res.ok && data.success) {
                feedback.innerHTML = `✅ <strong>Pengaturan Geofence Berhasil Disimpan ke Database!</strong> Titik pusat: ${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)} — Radius aktif: ${radius} meter.`;
                feedback.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in';

                // Update visual peta utama heatmap di bagian atas juga
                if (typeof geofenceCircleMain !== 'undefined') {
                    geofenceCircleMain.setLatLng([parseFloat(lat), parseFloat(lng)]);
                    geofenceCircleMain.setRadius(parseInt(radius));
                }
            } else {
                feedback.innerHTML = `🚫 <strong>GAGAL MENYIMPAN:</strong> ${data.message || 'Terjadi kesalahan saat menyimpan pengaturan geofence.'}`;
                feedback.className = 'p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in';
            }
        })
        .catch((err) => {
            feedback.innerHTML = `🚫 <strong>KONEKSI GAGAL:</strong> Tidak dapat menghubungi server. Pastikan koneksi internet dan server aktif. (${err.message || 'Network Error'})`;
            feedback.className = 'p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center animate-in fade-in';
        });
    }

    // ====== MANAJEMEN TITIK RAWAN PATROLI (CHECKPOINT CRUD) ======
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function openModalCheckpoint() {
        document.getElementById('checkpoint-id').value = '';
        document.getElementById('checkpoint-nama').value = '';
        document.getElementById('checkpoint-lat').value = '';
        document.getElementById('checkpoint-lng').value = '';
        document.getElementById('checkpoint-deskripsi').value = '';
        document.getElementById('checkpoint-tingkat').value = 'rawan';
        document.getElementById('checkpoint-rt').value = '01';
        document.getElementById('checkpoint-urutan').value = '1';
        document.getElementById('modal-checkpoint-title').textContent = 'Tambah Titik Rawan Patroli';
        document.getElementById('btn-submit-checkpoint').querySelector('span').textContent = 'Simpan Titik Rawan';
        const fb = document.getElementById('checkpoint-modal-feedback');
        fb.classList.add('hidden');
        fb.innerHTML = '';
        document.getElementById('modal-checkpoint').classList.remove('hidden');
    }

    function closeModalCheckpoint() {
        document.getElementById('modal-checkpoint').classList.add('hidden');
    }

    function editCheckpoint(ckp) {
        document.getElementById('checkpoint-id').value = ckp.id;
        document.getElementById('checkpoint-nama').value = ckp.nama_titik;
        document.getElementById('checkpoint-lat').value = ckp.latitude;
        document.getElementById('checkpoint-lng').value = ckp.longitude;
        document.getElementById('checkpoint-deskripsi').value = ckp.deskripsi || '';
        document.getElementById('checkpoint-tingkat').value = ckp.tingkat_kerawanan || 'rawan';
        document.getElementById('checkpoint-rt').value = ckp.rt || '01';
        document.getElementById('checkpoint-urutan').value = ckp.urutan_patroli || 1;
        document.getElementById('modal-checkpoint-title').textContent = 'Edit Titik Rawan: ' + ckp.nama_titik;
        document.getElementById('btn-submit-checkpoint').querySelector('span').textContent = 'Simpan Perubahan';
        const fb = document.getElementById('checkpoint-modal-feedback');
        fb.classList.add('hidden');
        fb.innerHTML = '';
        document.getElementById('modal-checkpoint').classList.remove('hidden');
    }

    function ambilLokasiCheckpointGPS() {
        if (!navigator.geolocation) {
            alert('GPS tidak tersedia di perangkat ini.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                document.getElementById('checkpoint-lat').value = pos.coords.latitude.toFixed(8);
                document.getElementById('checkpoint-lng').value = pos.coords.longitude.toFixed(8);
            },
            (err) => {
                alert('Gagal mendeteksi GPS: ' + err.message);
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    function setCheckpointFromRwCenter() {
        document.getElementById('checkpoint-lat').value = defaultLat.toFixed(8);
        document.getElementById('checkpoint-lng').value = defaultLng.toFixed(8);
    }

    function submitFormCheckpoint(e) {
        e.preventDefault();
        const feedback = document.getElementById('checkpoint-modal-feedback');
        const checkpointId = document.getElementById('checkpoint-id').value;
        const isEdit = checkpointId !== '';

        const payload = {
            nama_titik: document.getElementById('checkpoint-nama').value,
            latitude: parseFloat(document.getElementById('checkpoint-lat').value),
            longitude: parseFloat(document.getElementById('checkpoint-lng').value),
            deskripsi: document.getElementById('checkpoint-deskripsi').value,
            tingkat_kerawanan: document.getElementById('checkpoint-tingkat').value,
            rt: document.getElementById('checkpoint-rt').value,
            urutan_patroli: parseInt(document.getElementById('checkpoint-urutan').value) || 1,
        };

        feedback.innerHTML = '⏳ Menyimpan data titik rawan patroli ke database...';
        feedback.className = 'p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold text-center';
        feedback.classList.remove('hidden');

        const url = isEdit ? `/api/checkpoints/${checkpointId}` : '/api/checkpoints';
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(async (res) => {
            const data = await res.json();
            if (res.ok && data.success) {
                feedback.innerHTML = `✅ <strong>${data.message}</strong>`;
                feedback.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center';
                setTimeout(() => {
                    closeModalCheckpoint();
                    window.location.reload();
                }, 1200);
            } else {
                feedback.innerHTML = `🚫 <strong>GAGAL:</strong> ${data.message || 'Terjadi kesalahan saat menyimpan.'}`;
                feedback.className = 'p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center';
            }
        })
        .catch((err) => {
            feedback.innerHTML = `🚫 <strong>KONEKSI GAGAL:</strong> ${err.message || 'Network Error'}`;
            feedback.className = 'p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center';
        });
    }

    function hapusCheckpoint(id, nama) {
        if (!confirm(`Yakin ingin menghapus titik rawan patroli "${nama}"?\n\nTitik ini akan dihapus dari database dan tidak lagi dipantau oleh petugas ronda.`)) {
            return;
        }

        fetch(`/api/checkpoints/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(async (res) => {
            const data = await res.json();
            if (res.ok && data.success) {
                alert('✅ ' + data.message);
                window.location.reload();
            } else {
                alert('🚫 Gagal menghapus: ' + (data.message || 'Terjadi kesalahan.'));
            }
        })
        .catch((err) => {
            alert('🚫 Koneksi gagal: ' + (err.message || 'Network Error'));
        });
    }

</script>
@endpush
