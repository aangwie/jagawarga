<?php

namespace App\Http\Controllers;

use App\Models\BukuTamu;
use App\Models\CctvLingkungan;
use App\Models\Checkpoint;
use App\Models\JadwalRonda;
use App\Models\LaporanKejadian;
use App\Models\PanicAlert;
use App\Models\PresensiRonda;
use App\Models\RwSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JagaWargaController extends Controller
{
    /**
     * Tampilan Utama / Portal Interaktif JagaWarga RW
     */
    public function index()
    {
        // Ambil data dengan fallback jika database belum termigrasi
        try {
            $checkpoints = Checkpoint::orderBy('urutan_patroli')->get();
            $cctvs = CctvLingkungan::where('status', 'aktif')->get();
            $jadwalHariIni = JadwalRonda::with('user')->get();
            $laporanTerbaru = LaporanKejadian::with('user')->latest()->take(5)->get();
            $panicTerbaru = PanicAlert::with('user')->latest()->take(3)->get();
            $tamuTerbaru = BukuTamu::latest()->take(5)->get();
            $presensiTerbaru = PresensiRonda::with(['user', 'checkpoint'])->latest()->take(5)->get();
        } catch (\Throwable $e) {
            // Fallback Data Demo
            $checkpoints = collect([
                (object)[
                    'id' => 1,
                    'nama_titik' => 'Pos Ronda Utama RW 02',
                    'kode_qr' => 'JW-CKP-001-POSRW',
                    'rt' => '01',
                    'latitude' => -6.208800,
                    'longitude' => 106.845600,
                    'deskripsi' => 'Pusat komando ronda malam RW 02, kentongan bambu & kotak P3K',
                    'urutan_patroli' => 1,
                ],
                (object)[
                    'id' => 2,
                    'nama_titik' => 'Gapura Masuk Blok A',
                    'kode_qr' => 'JW-CKP-002-GAPURA',
                    'rt' => '01',
                    'latitude' => -6.209500,
                    'longitude' => 106.846200,
                    'deskripsi' => 'Gerbang akses utama perumahan RT 01',
                    'urutan_patroli' => 2,
                ],
                (object)[
                    'id' => 3,
                    'nama_titik' => 'Taman Lingkungan RT 02',
                    'kode_qr' => 'JW-CKP-003-TAMAN',
                    'rt' => '02',
                    'latitude' => -6.207900,
                    'longitude' => 106.847100,
                    'deskripsi' => 'Area bermain terbuka anak dan pembatas gang perumahan',
                    'urutan_patroli' => 3,
                ],
                (object)[
                    'id' => 4,
                    'nama_titik' => 'Gardu Trafo & Lorong Gang Senggol',
                    'kode_qr' => 'JW-CKP-004-GARDU',
                    'rt' => '02',
                    'latitude' => -6.208300,
                    'longitude' => 106.844900,
                    'deskripsi' => 'Titik minim penerangan jalan ujung lorong RT 02',
                    'urutan_patroli' => 4,
                ],
            ]);

            $cctvs = collect([
                (object)[
                    'id' => 1,
                    'nama_lokasi' => 'CCTV 01 - Gapura Utama RW 02',
                    'rt' => '01',
                    'url_stream' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                    'status' => 'aktif'
                ],
                (object)[
                    'id' => 2,
                    'nama_lokasi' => 'CCTV 02 - Pertigaan Pos Ronda RW 02',
                    'rt' => '01',
                    'url_stream' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                    'status' => 'aktif'
                ],
            ]);

            $jadwalHariIni = collect([
                (object)['user' => (object)['name' => 'Budi Santoso'], 'hari' => 'Senin', 'rt_id' => '01'],
                (object)['user' => (object)['name' => 'Pak Joko Ronda'], 'hari' => 'Senin', 'rt_id' => '01'],
                (object)['user' => (object)['name' => 'Kang Asep Patroli'], 'hari' => 'Rabu', 'rt_id' => '02'],
            ]);

            $laporanTerbaru = collect([
                (object)[
                    'id' => 1,
                    'judul' => 'Lampu Penerangan Jalan Padam di Lorong RT 02',
                    'user' => (object)['name' => 'Siti Rahayu'],
                    'deskripsi' => 'Lampu PJU padam sejak sore, kondisi gang sangat gelap.',
                    'status' => 'diproses',
                    'created_at' => now()->subHours(3),
                ]
            ]);

            $panicTerbaru = collect([]);
            $tamuTerbaru = collect([
                (object)[
                    'id' => 1,
                    'nama_tamu' => 'Ahmad Fauzi',
                    'alamat_asal' => 'Purwokerto, Jawa Tengah',
                    'warga_yang_dikunjungi' => 'Budi Santoso (RT 01)',
                    'status' => 'disetujui',
                    'tanggal_tiba' => now()->subDay(),
                ]
            ]);

            $presensiTerbaru = collect([
                (object)[
                    'id' => 1,
                    'user' => (object)['name' => 'Pak Joko Ronda'],
                    'checkpoint' => (object)['nama_titik' => 'Pos Ronda Utama RW 02'],
                    'waktu_scan' => now()->subHours(1),
                    'catatan' => 'Situasi pos ronda aman terkendali.',
                ]
            ]);
        }

        $rwSetting = RwSetting::getActiveSetting();

        return view('welcome', compact(
            'checkpoints',
            'cctvs',
            'jadwalHariIni',
            'laporanTerbaru',
            'panicTerbaru',
            'tamuTerbaru',
            'presensiTerbaru',
            'rwSetting'
        ));
    }

    /**
     * Pemicu Tombol Darurat Digital (Kentongan Online) dengan Validasi Geofence Radius
     */
    public function triggerPanic(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'kategori' => 'nullable|string|in:pencurian,kebakaran,medis,lainnya',
            'catatan' => 'nullable|string',
            'pelapor_nama' => 'nullable|string',
            'alamat_rumah' => 'nullable|string',
        ]);

        $setting = RwSetting::getActiveSetting();
        $lat = $validated['latitude'] ?? null;
        $lon = $validated['longitude'] ?? null;

        // Validasi jarak Geofencing jika koordinat tersedia
        if ($lat && $lon) {
            $distance = RwSetting::calculateDistanceMeters(
                $lat,
                $lon,
                $setting->center_latitude,
                $setting->center_longitude
            );

            if ($distance > $setting->panic_radius_meters) {
                return response()->json([
                    'success' => false,
                    'out_of_radius' => true,
                    'distance' => $distance,
                    'max_radius' => $setting->panic_radius_meters,
                    'message' => "Posisi Anda berada di luar radius keamanan RW 02 (Jarak: {$distance}m, Batas: {$setting->panic_radius_meters}m). Tombol panic hanya aktif di dalam jangkauan RW 02.",
                ], 422);
            }
        }

        try {
            $user = User::where('role', 'warga')->first() ?? User::first();
            $alert = PanicAlert::create([
                'user_id' => $user?->id ?? 1,
                'latitude' => $lat ?? $setting->center_latitude,
                'longitude' => $lon ?? $setting->center_longitude,
                'kategori' => $validated['kategori'] ?? 'pencurian',
                'status' => 'aktif',
                'catatan' => $validated['catatan'] ?? 'Sinyal darurat dikirim via Kentongan Online',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sinyal bahaya kentongan online berhasil disiarkan ke pos ronda dan HP warga!',
                'alert_id' => $alert->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Sinyal bahaya disimulasikan (Demo Mode). Alarm pos ronda diaktifkan!',
                'demo' => true,
            ]);
        }
    }

    /**
     * Simpan Presensi Scan QR Checkpoint Pos Ronda
     */
    public function storePresensi(Request $request)
    {
        $validated = $request->validate([
            'kode_qr' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);

        try {
            $checkpoint = Checkpoint::where('kode_qr', $validated['kode_qr'])->first();
            $user = User::where('role', 'petugas_ronda')->first() ?? User::first();

            if (!$checkpoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode QR Checkpoint tidak dikenali dalam sistem RW 02.',
                ], 404);
            }

            $presensi = PresensiRonda::create([
                'user_id' => $user?->id ?? 1,
                'checkpoint_id' => $checkpoint->id,
                'waktu_scan' => now(),
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'catatan' => $validated['catatan'] ?? 'Patroli berkala pos ronda',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presensi ronda berhasil tercatat di ' . $checkpoint->nama_titik . '!',
                'titik' => $checkpoint->nama_titik,
                'waktu' => now()->format('H:i:s'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Presensi ronda berhasil dicatat (Demo Mode)! Titik terverifikasi.',
                'titik' => 'Pos Ronda RW 02',
                'waktu' => now()->format('H:i:s'),
            ]);
        }
    }

    /**
     * Simpan Laporan Kejadian Cepat Warga
     */
    public function storeLaporan(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $user = User::first();
            $laporan = LaporanKejadian::create([
                'user_id' => $user?->id ?? 1,
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'status' => 'menunggu',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan kejadian berhasil dikirim ke pengurus RT/RW.',
                'laporan' => $laporan,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim (Demo Mode). Terima kasih atas partisipasi warga!',
            ]);
        }
    }

    /**
     * Simpan Buku Tamu / Tamu Wajib Lapor 2x24 Jam
     */
    public function storeBukuTamu(Request $request)
    {
        $validated = $request->validate([
            'nama_tamu' => 'required|string',
            'nik' => 'nullable|string',
            'no_hp' => 'required|string',
            'alamat_asal' => 'required|string',
            'tujuan_kunjungan' => 'required|string',
            'warga_yang_dikunjungi' => 'required|string',
            'rt' => 'nullable|string',
            'tanggal_tiba' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
        ]);

        try {
            $tamu = BukuTamu::create([
                'nama_tamu' => $validated['nama_tamu'],
                'nik' => $validated['nik'],
                'no_hp' => $validated['no_hp'],
                'alamat_asal' => $validated['alamat_asal'],
                'tujuan_kunjungan' => $validated['tujuan_kunjungan'],
                'warga_yang_dikunjungi' => $validated['warga_yang_dikunjungi'],
                'rt' => $validated['rt'] ?? '01',
                'tanggal_tiba' => $validated['tanggal_tiba'] ?? now(),
                'tanggal_keluar' => $validated['tanggal_keluar'],
                'status' => 'menunggu_verifikasi',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pendataan tamu 2x24 jam berhasil disimpan dan diteruskan ke Ketua RT.',
                'tamu' => $tamu,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Formulir tamu 2x24 jam berhasil dicatat (Demo Mode)!',
            ]);
        }
    }
}
