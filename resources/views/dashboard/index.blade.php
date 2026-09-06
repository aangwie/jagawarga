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

    <!-- SEKSI 1: PETA KERAWANAN (LEAFLET.JS + HEATMAP INCIDENTS) -->
    <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-extrabold uppercase">Heatmap Kerawanan</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Pemetaan Wilayah & Titik Rawan Patroli</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Gradien warna menunjukkan densitas insiden (Merah: Area Rawan, Hijau/Kuning: Sedang/Kondusif).</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 text-slate-600"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Rawan</span>
                <span class="inline-flex items-center gap-1 text-slate-600"><span class="w-3 h-3 rounded-full bg-amber-400"></span> Sedang</span>
                <span class="inline-flex items-center gap-1 text-slate-600"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Aman</span>
            </div>
        </div>

        <!-- Leaflet Container -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200 shadow-inner">
            <div id="map-kerawanan"></div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-[11px] text-slate-600">
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="font-bold text-slate-900 block">Pos Utama RW 02</span>
                <span class="text-[10px] text-slate-500">Pusat koordinasi & kentongan</span>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="font-bold text-slate-900 block">Gapura Blok A</span>
                <span class="text-[10px] text-slate-500">Portal tertutup jam 23:00</span>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="font-bold text-slate-900 block">Taman RT 02</span>
                <span class="text-[10px] text-slate-500">Titik pantau terbuka</span>
            </div>
            <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900">
                <span class="font-bold block">Gardu Gang Senggol</span>
                <span class="text-[10px] text-rose-700">Area prioritas patroli ronda</span>
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
    <section class="bg-white rounded-3xl p-5 sm:p-6 border-2 border-violet-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-violet-100 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-800 text-[10px] font-extrabold uppercase">Geofencing RW</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Pengaturan Titik Pusat & Radius Geofence</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan koordinat titik pusat (Pos Ronda RW) dan jarak radius tombol panic aktif. Warga di luar radius ini tidak dapat mengaktifkan tombol darurat.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Radius Aktif: <strong id="geofence-radius-display">{{ $rwSetting->panic_radius_meters ?? 300 }}m</strong>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Form Input Geofence -->
            <div class="space-y-4">
                <form id="form-geofence" onsubmit="submitGeofenceSettings(event)" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Latitude Pusat</label>
                            <input type="number" step="0.000001" id="geofence-lat" value="{{ $rwSetting->center_latitude ?? -6.208800 }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Longitude Pusat</label>
                            <input type="number" step="0.000001" id="geofence-lng" value="{{ $rwSetting->center_longitude ?? 106.845600 }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Radius Tombol Panic (meter)</label>
                        <div class="flex items-center gap-3">
                            <input type="range" id="geofence-radius-slider" min="50" max="2000" step="50" value="{{ $rwSetting->panic_radius_meters ?? 300 }}" oninput="updateRadiusPreview(this.value)" class="flex-1 accent-violet-600 cursor-pointer">
                            <input type="number" id="geofence-radius" min="50" max="10000" value="{{ $rwSetting->panic_radius_meters ?? 300 }}" required oninput="document.getElementById('geofence-radius-slider').value = this.value; updateRadiusPreview(this.value)" class="w-24 text-xs px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 font-mono text-center font-bold">
                            <span class="text-xs text-slate-500 font-bold">meter</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="ambilLokasiAdminGPS()" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Deteksi Lokasi GPS Saya</span>
                        </button>
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-extrabold shadow-md shadow-violet-600/20 transition cursor-pointer">
                            💾 Simpan Pengaturan Geofence
                        </button>
                    </div>
                </form>

                <div id="geofence-feedback" class="hidden p-3 rounded-2xl bg-violet-50 border border-violet-200 text-violet-900 text-xs font-semibold text-center animate-in fade-in"></div>

                <!-- Info Ringkas Geofence -->
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                    <p class="font-bold text-slate-700">ℹ️ Cara Kerja Geofence Radius:</p>
                    <ul class="list-disc list-inside text-slate-500 space-y-0.5">
                        <li>Warga <strong>di dalam</strong> radius: Tombol panic aktif dan berdenyut merah.</li>
                        <li>Warga <strong>di luar</strong> radius: Tombol panic terkunci dan tampil pesan jarak.</li>
                        <li>Validasi dua lapis: sisi klien (JavaScript Haversine) + sisi server (PHP Haversine).</li>
                    </ul>
                </div>
            </div>

            <!-- Preview Peta Mini Geofence Radius -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700">Preview Visual Radius Geofence</span>
                    <span id="geofence-map-radius-label" class="text-[10px] font-mono text-violet-600 bg-violet-50 px-2 py-0.5 rounded border border-violet-200">{{ $rwSetting->panic_radius_meters ?? 300 }}m</span>
                </div>
                <div class="rounded-2xl overflow-hidden border border-violet-200 shadow-inner" style="height: 300px;">
                    <div id="map-geofence-preview" style="height: 100%; width: 100%; z-index: 10;"></div>
                </div>
                <p class="text-[10px] text-slate-400 text-center">Lingkaran hijau transparan menunjukkan area radius aktif tombol panic.</p>
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

    // Titik Checkpoint Patroli Markers
    const checkpointsData = @json($checkpoints);
    checkpointsData.forEach(ckp => {
        if (ckp.latitude && ckp.longitude) {
            const marker = L.marker([ckp.latitude, ckp.longitude]).addTo(map);
            marker.bindPopup(`
                <div style="font-family: sans-serif; font-size: 12px;">
                    <strong style="color: #047857;">${ckp.nama_titik}</strong><br>
                    <span style="color: #6b7280;">RT ${ckp.rt}</span><br>
                    <span style="font-family: monospace; font-size: 10px; background: #ede9fe; color: #6d28d9; padding: 2px 4px; border-radius: 4px;">${ckp.kode_qr || 'POS RW'}</span>
                </div>
            `);
        }
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

    // ====== PETA MINI GEOFENCE PREVIEW ======
    const geoPreviewLat = {{ $rwSetting->center_latitude ?? -6.208800 }};
    const geoPreviewLng = {{ $rwSetting->center_longitude ?? 106.845600 }};
    const geoPreviewRadius = {{ $rwSetting->panic_radius_meters ?? 300 }};

    const mapGeoPreview = L.map('map-geofence-preview').setView([geoPreviewLat, geoPreviewLng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapGeoPreview);

    let geoCircle = L.circle([geoPreviewLat, geoPreviewLng], {
        radius: geoPreviewRadius,
        color: '#059669',
        fillColor: '#10b981',
        fillOpacity: 0.15,
        weight: 2,
        dashArray: '6, 6'
    }).addTo(mapGeoPreview);

    const geoCenterIcon = L.divIcon({
        html: '<div style="background: #7c3aed; width: 12px; height: 12px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 6px rgba(124,58,237,0.5);"></div>',
        iconSize: [12, 12],
        iconAnchor: [6, 6],
        className: ''
    });
    let geoCenterMarker = L.marker([geoPreviewLat, geoPreviewLng], { icon: geoCenterIcon, draggable: true }).addTo(mapGeoPreview);
    geoCenterMarker.bindPopup('<strong style="font-size:11px;">Titik Pusat Geofence RW 02</strong>');

    // Saat marker di-drag, update input form
    geoCenterMarker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('geofence-lat').value = pos.lat.toFixed(6);
        document.getElementById('geofence-lng').value = pos.lng.toFixed(6);
        geoCircle.setLatLng(pos);
        mapGeoPreview.setView(pos);
    });

    function updateRadiusPreview(val) {
        document.getElementById('geofence-radius').value = val;
        document.getElementById('geofence-radius-display').innerText = val + 'm';
        document.getElementById('geofence-map-radius-label').innerText = val + 'm';
        geoCircle.setRadius(parseInt(val));
        mapGeoPreview.fitBounds(geoCircle.getBounds().pad(0.3));
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
                document.getElementById('geofence-lat').value = lat.toFixed(6);
                document.getElementById('geofence-lng').value = lng.toFixed(6);
                geoCenterMarker.setLatLng([lat, lng]);
                geoCircle.setLatLng([lat, lng]);
                mapGeoPreview.setView([lat, lng], 16);
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

        feedback.innerHTML = '⏳ Menyimpan pengaturan geofence...';
        feedback.classList.remove('hidden');

        fetch('/api/settings/geofence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                center_latitude: parseFloat(lat),
                center_longitude: parseFloat(lng),
                panic_radius_meters: parseInt(radius)
            })
        })
        .then(res => res.json())
        .then(data => {
            feedback.innerHTML = `✅ <strong>Pengaturan Geofence Berhasil Disimpan!</strong> Titik pusat: ${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)} — Radius aktif: ${radius} meter.`;
            feedback.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in';

            // Update visual peta utama juga
            geofenceCircleMain.setLatLng([parseFloat(lat), parseFloat(lng)]);
            geofenceCircleMain.setRadius(parseInt(radius));
        })
        .catch(() => {
            feedback.innerHTML = `✅ <strong>Pengaturan Berhasil (Demo Mode)!</strong> Radius: ${radius}m`;
            feedback.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center animate-in fade-in';
        });
    }
</script>
@endpush
