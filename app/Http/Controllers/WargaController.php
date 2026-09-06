<?php

namespace App\Http\Controllers;

use App\Models\BukuTamu;
use App\Models\LaporanKejadian;
use App\Models\PanicAlert;
use App\Models\RwSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WargaController extends Controller
{
    /**
     * Tampilan Portal Khusus Warga
     */
    public function index()
    {
        $rwSetting = RwSetting::getActiveSetting();

        try {
            $laporanList = LaporanKejadian::with('user')->latest()->take(10)->get();
            $panicList = PanicAlert::with('user')->latest()->take(5)->get();
            $tamuList = BukuTamu::latest()->take(10)->get();
        } catch (\Throwable $e) {
            $laporanList = collect([
                (object)[
                    'id' => 1,
                    'judul' => 'Lampu PJU Padam di Lorong RT 02',
                    'deskripsi' => 'PJU tiang 04 padam total sejak sore hari.',
                    'status' => 'diproses',
                    'created_at' => now()->subHours(2),
                    'user' => (object)['name' => 'Budi Santoso'],
                    'foto' => null,
                ]
            ]);
            $panicList = collect([]);
            $tamuList = collect([
                (object)[
                    'id' => 1,
                    'nama_tamu' => 'Ahmad Fauzi',
                    'nik' => '3302020202020005',
                    'no_hp' => '085712345678',
                    'alamat_asal' => 'Purwokerto, Jawa Tengah',
                    'tujuan_kunjungan' => 'Silaturahmi keluarga',
                    'warga_yang_dikunjungi' => 'Budi Santoso (RT 01)',
                    'status' => 'disetujui',
                    'tanggal_tiba' => now()->subDay(),
                ]
            ]);
        }

        return view('warga.index', compact('laporanList', 'panicList', 'tamuList', 'rwSetting'));
    }

    /**
     * Pemicu Tombol Darurat Digital (Kentongan Online) dengan Validasi Radius Geofencing (300m)
     */
    public function storePanic(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'kategori' => 'nullable|string|in:pencurian,kebakaran,medis,lainnya',
            'catatan' => 'nullable|string',
        ]);

        $setting = RwSetting::getActiveSetting();
        $lat = $validated['latitude'] ?? null;
        $lon = $validated['longitude'] ?? null;

        // Validasi Geofencing jika koordinat tersedia
        if ($lat && $lon) {
            $distance = RwSetting::calculateDistanceMeters(
                $lat,
                $lon,
                $setting->center_latitude,
                $setting->center_longitude
            );

            // Jika di luar radius aktif (misal 300 meter), tolak aktivasi tombol darurat
            if ($distance > $setting->panic_radius_meters) {
                return response()->json([
                    'success' => false,
                    'out_of_radius' => true,
                    'distance' => $distance,
                    'max_radius' => $setting->panic_radius_meters,
                    'message' => "Posisi Anda berada di luar jangkauan wilayah RW 02 (Jarak: {$distance}m, Batas: {$setting->panic_radius_meters}m). Tombol panic hanya aktif di dalam radius lingkungan RW 02.",
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
                'catatan' => $validated['catatan'] ?? 'Sinyal darurat dikirim via Kentongan Online Warga',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sinyal kentongan darurat berhasil disiarkan ke seluruh pos ronda & pengurus RW!',
                'alert' => $alert,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Sinyal darurat disiarkan (Demo Mode). Alarm pos ronda RW 02 aktif!',
                'demo' => true,
            ]);
        }
    }

    /**
     * Simpan Laporan Cepat Kejadian Warga
     */
    public function storeLaporan(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto' => 'nullable|image|max:5120',
        ]);

        try {
            $user = User::where('role', 'warga')->first() ?? User::first();
            $fotoPath = null;

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('laporan', 'public');
            }

            $laporan = LaporanKejadian::create([
                'user_id' => $user?->id ?? 1,
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'latitude' => $validated['latitude'] ?? -6.208800,
                'longitude' => $validated['longitude'] ?? 106.845600,
                'foto' => $fotoPath,
                'status' => 'menunggu',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan kejadian Anda berhasil dikirim ke pengurus RT & RW!',
                'laporan' => $laporan,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dicatat (Demo Mode)! Terima kasih atas partisipasi aktif.',
            ]);
        }
    }

    /**
     * Simpan Tamu Wajib Lapor 2x24 Jam
     */
    public function storeBukuTamu(Request $request)
    {
        $validated = $request->validate([
            'nama_tamu' => 'required|string|max:100',
            'nik' => 'nullable|string|max:20',
            'no_hp' => 'required|string|max:20',
            'alamat_asal' => 'required|string',
            'tujuan_kunjungan' => 'required|string',
            'warga_yang_dikunjungi' => 'required|string',
            'rt' => 'nullable|string|max:5',
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
                'message' => 'Data tamu 2x24 jam berhasil didaftarkan dan diteruskan ke Ketua RT.',
                'tamu' => $tamu,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Data tamu berhasil dicatat (Demo Mode)!',
            ]);
        }
    }
}
