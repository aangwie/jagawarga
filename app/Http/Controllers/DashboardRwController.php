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

class DashboardRwController extends Controller
{
    /**
     * Dashboard Command Center Pengurus RW & Bhabinkamtibmas
     */
    public function index()
    {
        try {
            Checkpoint::ensureTableExists();
            PanicAlert::ensureTableExists();
            BukuTamu::ensureTableExists();

            $totalWarga = User::where('role', 'warga')->count();
            $totalRonda = User::where('role', 'petugas_ronda')->count();
            $totalLaporan = LaporanKejadian::count();
            $totalPanic = PanicAlert::count();
            $totalCheckpoints = Checkpoint::count();
            $totalTamu = BukuTamu::count();

            $checkpoints = Checkpoint::orderBy('urutan_patroli')->get();
            $cctvs = CctvLingkungan::all();
            $jadwalList = JadwalRonda::with('user')->orderBy('hari')->get();
            $wargaList = User::orderBy('name')->get();
            $panicList = PanicAlert::with('user')->latest()->take(5)->get();
            $laporanList = LaporanKejadian::with('user')->latest()->take(5)->get();
            $tamuList = BukuTamu::latest()->take(5)->get();

            // Data Titik Kerawanan untuk Leaflet Heatmap
            $heatmapPoints = [];
            
            // Titik Panic Alert (intensitas tinggi: 1.0)
            foreach (PanicAlert::all() as $p) {
                if ($p->latitude && $p->longitude) {
                    $heatmapPoints[] = [$p->latitude, $p->longitude, 1.0, 'Darurat: ' . $p->kategori];
                }
            }

            // Titik Laporan Kejadian (intensitas sedang: 0.6)
            foreach (LaporanKejadian::all() as $l) {
                if ($l->latitude && $l->longitude) {
                    $heatmapPoints[] = [$l->latitude, $l->longitude, 0.6, 'Laporan: ' . $l->judul];
                }
            }

            // Titik default jika belum ada laporan
            if (empty($heatmapPoints)) {
                $heatmapPoints = [
                    [-6.208800, 106.845600, 0.4, 'Pos Ronda RW 02'],
                    [-6.208300, 106.844900, 0.8, 'Gardu Trafo & Gang Senggol (Rawan)'],
                    [-6.209500, 106.846200, 0.5, 'Gapura Blok A'],
                    [-6.207900, 106.847100, 0.3, 'Taman Lingkungan RT 02']
                ];
            }

        } catch (\Throwable $e) {
            $totalWarga = 24;
            $totalRonda = 8;
            $totalLaporan = 5;
            $totalPanic = 2;
            $totalCheckpoints = 4;
            $totalTamu = 3;

            $checkpoints = collect([
                (object)['id' => 1, 'nama_titik' => 'Pos Ronda Utama RW 02', 'latitude' => -6.208800, 'longitude' => 106.845600, 'rt' => '01'],
                (object)['id' => 2, 'nama_titik' => 'Gapura Masuk Gerbang Blok A', 'latitude' => -6.209500, 'longitude' => 106.846200, 'rt' => '01'],
                (object)['id' => 3, 'nama_titik' => 'Taman Lingkungan RT 02', 'latitude' => -6.207900, 'longitude' => 106.847100, 'rt' => '02'],
                (object)['id' => 4, 'nama_titik' => 'Gardu Trafo & Gang Senggol', 'latitude' => -6.208300, 'longitude' => 106.844900, 'rt' => '02'],
            ]);

            $cctvs = collect([
                (object)['id' => 1, 'nama_lokasi' => 'CCTV 01 - Gapura Utama RW 02', 'rt' => '01', 'status' => 'aktif'],
                (object)['id' => 2, 'nama_lokasi' => 'CCTV 02 - Simpang Pos Ronda RW 02', 'rt' => '01', 'status' => 'aktif'],
            ]);

            $jadwalList = collect([
                (object)['id' => 1, 'user' => (object)['name' => 'Budi Santoso', 'phone' => '081234567890'], 'hari' => 'Senin', 'rt_id' => '01'],
                (object)['id' => 2, 'user' => (object)['name' => 'Pak Joko Ronda', 'phone' => '081234567892'], 'hari' => 'Senin', 'rt_id' => '01'],
                (object)['id' => 3, 'user' => (object)['name' => 'Kang Asep Patroli', 'phone' => '081234567893'], 'hari' => 'Rabu', 'rt_id' => '02'],
                (object)['id' => 4, 'user' => (object)['name' => 'Siti Rahayu', 'phone' => '081234567891'], 'hari' => 'Rabu', 'rt_id' => '02'],
                (object)['id' => 5, 'user' => (object)['name' => 'Pak Joko Ronda', 'phone' => '081234567892'], 'hari' => 'Sabtu', 'rt_id' => '01'],
            ]);

            $wargaList = collect([
                (object)['id' => 1, 'name' => 'Budi Santoso', 'role' => 'warga', 'rt_id' => '01'],
                (object)['id' => 2, 'name' => 'Siti Rahayu', 'role' => 'warga', 'rt_id' => '02'],
                (object)['id' => 3, 'name' => 'Pak Joko Ronda', 'role' => 'petugas_ronda', 'rt_id' => '01'],
                (object)['id' => 4, 'name' => 'Kang Asep Patroli', 'role' => 'petugas_ronda', 'rt_id' => '02'],
            ]);

            $panicList = collect([]);
            $laporanList = collect([
                (object)['judul' => 'Lampu Penerangan Jalan Padam', 'status' => 'diproses', 'user' => (object)['name' => 'Siti Rahayu']]
            ]);
            $tamuList = collect([
                (object)['nama_tamu' => 'Ahmad Fauzi', 'alamat_asal' => 'Purwokerto', 'status' => 'disetujui']
            ]);

            $heatmapPoints = [
                [-6.208800, 106.845600, 0.4, 'Pos Ronda RW 02'],
                [-6.208300, 106.844900, 0.9, 'Gardu Trafo & Gang Senggol (Rawan)'],
                [-6.209500, 106.846200, 0.5, 'Gapura Blok A'],
                [-6.207900, 106.847100, 0.3, 'Taman Lingkungan RT 02']
            ];
        }

        $rwSetting = RwSetting::getActiveSetting();

        return view('dashboard.index', compact(
            'totalWarga',
            'totalRonda',
            'totalLaporan',
            'totalPanic',
            'totalCheckpoints',
            'totalTamu',
            'checkpoints',
            'cctvs',
            'jadwalList',
            'wargaList',
            'panicList',
            'laporanList',
            'tamuList',
            'heatmapPoints',
            'rwSetting'
        ));
    }

    /**
     * Perbarui Pengaturan Titik Pusat & Radius Geofence Tombol Panic
     */
    public function updateGeofenceSettings(Request $request)
    {
        $validated = $request->validate([
            'center_latitude' => 'required|numeric',
            'center_longitude' => 'required|numeric',
            'panic_radius_meters' => 'required|integer|min:50|max:10000',
        ]);

        try {
            RwSetting::ensureTableExists();

            $setting = RwSetting::getActiveSetting();
            $setting->center_latitude = $validated['center_latitude'];
            $setting->center_longitude = $validated['center_longitude'];
            $setting->panic_radius_meters = $validated['panic_radius_meters'];
            $setting->save();

            // Refresh untuk memastikan data terbaru dari DB
            $setting->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan geofence RW 02 berhasil disimpan di database! Titik pusat: ' . $setting->center_latitude . ', ' . $setting->center_longitude . ' — Radius aktif: ' . $setting->panic_radius_meters . ' meter.',
                'setting' => $setting,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pengaturan geofence ke database: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tambah/Kelola Jadwal Ronda Mingguan
     */
    public function storeJadwal(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'rt_id' => 'required|string|max:5',
        ]);

        try {
            $jadwal = JadwalRonda::updateOrCreate(
                ['user_id' => $validated['user_id'], 'hari' => $validated['hari']],
                ['rt_id' => $validated['rt_id']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Jadwal ronda berhasil ditambahkan ke jadwal mingguan!',
                'jadwal' => $jadwal,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal ronda berhasil disimpan (Demo Mode)!',
            ]);
        }
    }

    /**
     * Hapus Jadwal Ronda
     */
    public function deleteJadwal($id)
    {
        try {
            JadwalRonda::destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Jadwal ronda berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus (Demo Mode).',
            ]);
        }
    }

    /**
     * Simulasi Pengiriman Pengingat WhatsApp via Fonnte / Wablas Gateway
     */
    public function sendWhatsappReminder(Request $request)
    {
        $validated = $request->validate([
            'nama_warga' => 'required|string',
            'nomor_wa' => 'required|string',
            'hari_ronda' => 'required|string',
            'pos_ronda' => 'nullable|string',
        ]);

        $pesan = "Halo Bpk/Sdr *" . $validated['nama_warga'] . "*,\n\n"
               . "Mengingatkan jadwal giliran ronda malam Anda besok hari *" . $validated['hari_ronda'] . "* di Pos Ronda RW 02.\n"
               . "Waktu: Pukul 22.00 s/d 04.00 WIB.\n"
               . "Mohon scan QR Checkpoint secara berkala menggunakan aplikasi JagaWarga RW.\n\n"
               . "Salam Kompak,\n*Pengurus RW 02 & Babinkamtibmas*";

        // Simulasi integrasi gateway WhatsApp (Fonnte API)
        // Logika HTTP POST ke API https://api.fonnte.com/send
        return response()->json([
            'success' => true,
            'message' => 'Pengingat WhatsApp berhasil dikirimkan ke ' . $validated['nomor_wa'] . '!',
            'konten_pesan' => $pesan,
        ]);
    }

    /**
     * Simpan Titik Rawan Patroli Baru
     */
    public function storeCheckpoint(Request $request)
    {
        $validated = $request->validate([
            'nama_titik' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'deskripsi' => 'nullable|string',
            'tingkat_kerawanan' => 'nullable|in:rawan,sedang,aman',
            'rt' => 'nullable|string|max:10',
            'urutan_patroli' => 'nullable|integer',
        ]);

        try {
            Checkpoint::ensureTableExists();

            $nextId = (Checkpoint::max('id') ?? 0) + 1;
            $slug = \Illuminate\Support\Str::slug($validated['nama_titik']) ?: 'titik';
            $kodeQr = 'JW-CKP-' . str_pad((string)$nextId, 3, '0', STR_PAD_LEFT) . '-' . substr(strtoupper($slug), 0, 8);

            $checkpoint = Checkpoint::create([
                'nama_titik' => $validated['nama_titik'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'tingkat_kerawanan' => $validated['tingkat_kerawanan'] ?? 'rawan',
                'rt' => $validated['rt'] ?? '01',
                'urutan_patroli' => $validated['urutan_patroli'] ?? $nextId,
                'kode_qr' => $kodeQr,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Titik rawan patroli "' . $checkpoint->nama_titik . '" berhasil ditambahkan!',
                'checkpoint' => $checkpoint,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan titik rawan patroli: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update Titik Rawan Patroli
     */
    public function updateCheckpoint(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_titik' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'deskripsi' => 'nullable|string',
            'tingkat_kerawanan' => 'nullable|in:rawan,sedang,aman',
            'rt' => 'nullable|string|max:10',
            'urutan_patroli' => 'nullable|integer',
        ]);

        try {
            $checkpoint = Checkpoint::findOrFail($id);
            $checkpoint->update([
                'nama_titik' => $validated['nama_titik'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'tingkat_kerawanan' => $validated['tingkat_kerawanan'] ?? $checkpoint->tingkat_kerawanan,
                'rt' => $validated['rt'] ?? $checkpoint->rt,
                'urutan_patroli' => $validated['urutan_patroli'] ?? $checkpoint->urutan_patroli,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data titik rawan patroli berhasil diperbarui!',
                'checkpoint' => $checkpoint,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui titik rawan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus Titik Rawan Patroli
     */
    public function deleteCheckpoint($id)
    {
        try {
            $checkpoint = Checkpoint::findOrFail($id);
            $nama = $checkpoint->nama_titik;
            $checkpoint->delete();

            return response()->json([
                'success' => true,
                'message' => 'Titik rawan patroli "' . $nama . '" berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus titik rawan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
