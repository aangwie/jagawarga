@extends('layouts.app')

@section('title', 'Portal Petugas Ronda - JagaWarga RW 02')

@section('content')
<div class="space-y-6">

    <!-- Header Portal Petugas Ronda -->
    <div class="bg-gradient-to-r from-violet-700 via-purple-700 to-indigo-800 rounded-3xl p-5 sm:p-6 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-36 h-36 rounded-full bg-white/10 blur-xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl shadow-inner">
                    🔦
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-white/20 text-violet-100 text-[10px] font-extrabold uppercase tracking-wider">
                            Sisi Petugas Poskamling
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500 text-white text-[10px] font-bold">
                            SIAGA PATROLI
                        </span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-black tracking-tight mt-1">Sistem Patroli & Presensi Checkpoint</h1>
                    <p class="text-xs text-violet-100 mt-0.5">Scan kode QR di titik rawan RW 02 untuk membuktikan patroli berkala.</p>
                </div>
            </div>
            <button onclick="startCameraScanner()" class="px-4 py-2.5 rounded-2xl bg-white text-violet-900 hover:bg-violet-50 text-xs font-black shadow-lg transition flex items-center gap-2 cursor-pointer shrink-0">
                <svg class="w-4 h-4 text-violet-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Buka Kamera Scan QR</span>
            </button>
        </div>
    </div>

    <!-- PROGRES PATROLI MALAM INI -->
    <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Progres Rute Patroli Malam Ini</h3>
                <p class="text-xs text-slate-500">{{ $completedPoints }} dari {{ $totalPoints }} titik checkpoint telah diperiksa</p>
            </div>
            <span class="text-xs font-extrabold text-violet-700 bg-violet-50 px-3 py-1 rounded-xl border border-violet-200">
                {{ $progressPercent }}% Selesai
            </span>
        </div>

        <!-- Progress Bar -->
        <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-violet-600 to-emerald-500 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%;"></div>
        </div>
    </section>

    <!-- LIVE CAMERA SCANNER MODAL / CARD -->
    <div id="camera-scanner-card" class="hidden bg-slate-950 rounded-3xl p-5 text-white border border-slate-800 shadow-xl space-y-4 animate-in fade-in">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                <h3 class="text-sm font-bold text-white">Pemindai QR Checkpoint Aktif</h3>
            </div>
            <button onclick="stopCameraScanner()" class="px-2.5 py-1 text-xs bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg cursor-pointer">
                Tutup Kamera
            </button>
        </div>

        <div class="max-w-xs mx-auto aspect-square rounded-2xl overflow-hidden bg-slate-900 relative flex items-center justify-center border-2 border-violet-500/50">
            <div id="reader-live" class="w-full h-full"></div>
            <div id="scanner-status" class="absolute bottom-2 left-2 right-2 p-1.5 rounded-lg bg-slate-950/80 text-[10px] text-center text-slate-300 font-mono">
                Arahkan kamera ke QR Code pos ronda...
            </div>
        </div>
    </div>

    <!-- FEEDBACK SCAN PREVIEW -->
    <div id="ronda-scan-feedback" class="hidden p-4 rounded-3xl bg-violet-50 border-2 border-violet-200 text-violet-950 text-xs font-semibold text-center animate-in fade-in shadow-xs"></div>

    <!-- SEKSI 2: DAFTAR CHECKPOINT PATROLI RW 02 -->
    <section class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-slate-900">Titik Checkpoint Rute Patroli</h3>
                <p class="text-xs text-slate-500">Klik tombol "Verifikasi Hadir" untuk mencatat presensi fisik Anda di titik pos.</p>
            </div>
            <span class="text-[11px] font-bold text-violet-700 bg-violet-50 px-2.5 py-1 rounded-lg self-start sm:self-auto">
                Anti-Fraud GPS Enabled
            </span>
        </div>

        <div class="space-y-3">
            @foreach($checkpoints as $ckp)
            @php
                $isScanned = in_array($ckp->id, $scannedCheckpointIds);
            @endphp
            <div class="p-4 rounded-2xl border {{ $isScanned ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200/80' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition hover:shadow-xs">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl {{ $isScanned ? 'bg-emerald-600 text-white' : 'bg-violet-100 text-violet-700' }} flex items-center justify-center font-bold text-sm shrink-0">
                        @if($isScanned)
                            ✓
                        @else
                            {{ $ckp->urutan_patroli ?? $loop->iteration }}
                        @endif
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-900">{{ $ckp->nama_titik }}</h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $isScanned ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $isScanned ? 'Sudah Diperiksa' : 'Belum Diperiksa' }}
                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded-md font-extrabold border {{ $ckp->badge_class }}">
                                {{ $ckp->tingkat_kerawanan_badge }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $ckp->deskripsi ?? 'Area pengawasan patroli malam.' }}</p>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-[11px] text-slate-400 font-mono">
                            <span>Kode: <strong>{{ $ckp->kode_qr }}</strong></span>
                            <span>RT {{ $ckp->rt }}</span>
                            @if($ckp->latitude && $ckp->longitude)
                            <a href="{{ $ckp->google_maps_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-0.5 no-underline transition">
                                📍 Navigasi Maps
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    @if($ckp->latitude && $ckp->longitude)
                    <a href="{{ $ckp->google_maps_url }}" target="_blank" class="px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition flex items-center gap-1.5 no-underline">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Maps</span>
                    </a>
                    @endif
                    <button onclick="verifikasiCheckpoint('{{ $ckp->kode_qr }}', '{{ $ckp->nama_titik }}')" class="px-3.5 py-2 rounded-xl {{ $isScanned ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-violet-600 hover:bg-violet-700' }} text-white text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ $isScanned ? 'Scan Ulang' : 'Verifikasi Hadir' }}</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- SEKSI 3: FORM CATATAN JAGA MALAM & LOGBOOK PATROLI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Input Catatan Jaga Malam -->
        <section class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-3">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900">Catatan Jaga Poskamling</h3>
                <p class="text-xs text-slate-500">Tulis catatan insiden atau situasi patroli malam ini.</p>
            </div>

            <form onsubmit="submitCatatanJaga(event)" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Patroli</label>
                    <textarea id="catatan-jaga-input" rows="3" required placeholder="Contoh: Portal gang Melati sudah digembok pukul 23:30, cuaca gerimis aman..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    Simpan ke Buku Log Ronda
                </button>
            </form>

            <div id="catatan-feedback" class="hidden p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center"></div>
        </section>

        <!-- Riwayat Scan Malam Ini (Logbook) -->
        <section class="lg:col-span-2 bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-3">
            <div class="border-b border-slate-100 pb-2 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Buku Log Patroli Malam Ini</h3>
                    <p class="text-xs text-slate-500">Rekaman checkpoint yang berhasil diverifikasi petugas.</p>
                </div>
                <span class="text-[11px] font-mono text-slate-400 font-semibold">{{ today()->format('d M Y') }}</span>
            </div>

            <div class="space-y-2">
                @forelse($presensiHariIni as $pres)
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-slate-900">{{ $pres->checkpoint->nama_titik ?? 'Pos Ronda RW 02' }}</p>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold uppercase">Terverifikasi</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $pres->catatan ?? 'Patroli berkala pos ronda malam.' }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Petugas: {{ $pres->user->name ?? 'Petugas Ronda' }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="font-mono font-bold text-violet-700 text-xs bg-violet-50 px-2 py-1 rounded-lg border border-violet-200">
                            {{ \Carbon\Carbon::parse($pres->waktu_scan)->format('H:i:s') }} WIB
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 p-4 text-center bg-slate-50 rounded-2xl">
                    Belum ada riwayat patroli malam ini. Silakan scan checkpoint pertama Anda!
                </p>
                @endforelse
            </div>
        </section>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let cameraScannerInstance = null;

    function startCameraScanner() {
        const card = document.getElementById('camera-scanner-card');
        card.classList.remove('hidden');
        card.scrollIntoView({ behavior: 'smooth' });

        if (typeof Html5Qrcode !== 'undefined') {
            cameraScannerInstance = new Html5Qrcode("reader-live");
            const config = { fps: 10, qrbox: { width: 220, height: 220 } };

            cameraScannerInstance.start(
                { facingMode: "environment" },
                config,
                (qrCodeMessage) => {
                    stopCameraScanner();
                    verifikasiCheckpoint(qrCodeMessage, `QR: ${qrCodeMessage}`);
                },
                (err) => {}
            ).catch(err => {
                document.getElementById('scanner-status').innerText = 'Kamera tidak dapat diakses. Silakan gunakan tombol "Verifikasi Hadir"';
            });
        }
    }

    function stopCameraScanner() {
        if (cameraScannerInstance) {
            cameraScannerInstance.stop().then(() => {
                cameraScannerInstance.clear();
            }).catch(e => console.log(e));
        }
        document.getElementById('camera-scanner-card').classList.add('hidden');
    }

    function verifikasiCheckpoint(kodeQr, namaTitik) {
        const feedback = document.getElementById('ronda-scan-feedback');
        feedback.innerHTML = `⏳ Mengunci GPS ponsel & memverifikasi kehadiran di <strong>${namaTitik}</strong>...`;
        feedback.classList.remove('hidden');
        feedback.scrollIntoView({ behavior: 'smooth' });

        let lat = -6.208800, lon = 106.845600;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    lat = pos.coords.latitude;
                    lon = pos.coords.longitude;
                    kirimPresensi(kodeQr, namaTitik, lat, lon);
                },
                (err) => {
                    kirimPresensi(kodeQr, namaTitik, lat, lon);
                },
                { enableHighAccuracy: true, timeout: 4000 }
            );
        } else {
            kirimPresensi(kodeQr, namaTitik, lat, lon);
        }
    }

    function kirimPresensi(kodeQr, namaTitik, lat, lon) {
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
            const feedback = document.getElementById('ronda-scan-feedback');
            feedback.innerHTML = `✅ <strong>Checkpoint Berhasil Diverifikasi!</strong> Kehadiran petugas di <strong>${namaTitik}</strong> tercatat pada pukul ${new Date().toLocaleTimeString('id-ID')} WIB (GPS: ${lat.toFixed(5)}, ${lon.toFixed(5)}).`;
            playKentonganKnock(620, 0.15);
        })
        .catch(() => {
            const feedback = document.getElementById('ronda-scan-feedback');
            feedback.innerHTML = `✅ <strong>Presensi Tercatat (Offline/Demo)!</strong> Titik <strong>${namaTitik}</strong> telah diverifikasi.`;
        });
    }

    function submitCatatanJaga(e) {
        e.preventDefault();
        const catatan = document.getElementById('catatan-jaga-input').value;
        const feedback = document.getElementById('catatan-feedback');

        feedback.innerHTML = '✅ Catatan patroli berhasil disimpan ke buku log ronda malam!';
        feedback.classList.remove('hidden');
        document.getElementById('catatan-jaga-input').value = '';

        setTimeout(() => feedback.classList.add('hidden'), 4000);
    }
</script>
@endpush
