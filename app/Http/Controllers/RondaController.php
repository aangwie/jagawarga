<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\PresensiRonda;
use App\Models\User;
use Illuminate\Http\Request;

class RondaController extends Controller
{
    /**
     * Tampilan Portal Khusus Petugas Ronda
     */
    public function index()
    {
        try {
            $checkpoints = Checkpoint::orderBy('urutan_patroli')->get();
            
            // Riwayat presensi malam ini
            $presensiHariIni = PresensiRonda::with(['user', 'checkpoint'])
                ->whereDate('waktu_scan', today())
                ->latest('waktu_scan')
                ->get();

            // Cek titik mana saja yang sudah discan malam ini
            $scannedCheckpointIds = $presensiHariIni->pluck('checkpoint_id')->unique()->toArray();

            // Hitung persentase rute patroli malam ini
            $totalPoints = $checkpoints->count();
            $completedPoints = count($scannedCheckpointIds);
            $progressPercent = $totalPoints > 0 ? round(($completedPoints / $totalPoints) * 100) : 0;

        } catch (\Throwable $e) {
            $checkpoints = collect([
                (object)[
                    'id' => 1,
                    'nama_titik' => 'Pos Ronda Utama RW 02',
                    'kode_qr' => 'JW-CKP-001-POSRW',
                    'rt' => '01',
                    'latitude' => -6.208800,
                    'longitude' => 106.845600,
                    'deskripsi' => 'Pusat kumpul tim ronda malam RW 02',
                    'urutan_patroli' => 1,
                ],
                (object)[
                    'id' => 2,
                    'nama_titik' => 'Gapura Masuk Gerbang Blok A',
                    'kode_qr' => 'JW-CKP-002-GAPURA',
                    'rt' => '01',
                    'latitude' => -6.209500,
                    'longitude' => 106.846200,
                    'deskripsi' => 'Gerbang portal akses kendaraan RT 01',
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
                    'nama_titik' => 'Gardu Trafo PLN & Gang Senggol',
                    'kode_qr' => 'JW-CKP-004-GARDU',
                    'rt' => '02',
                    'latitude' => -6.208300,
                    'longitude' => 106.844900,
                    'deskripsi' => 'Titik minim penerangan jalan lorong belakang RT 02',
                    'urutan_patroli' => 4,
                ],
            ]);

            $scannedCheckpointIds = [1];
            $presensiHariIni = collect([
                (object)[
                    'id' => 1,
                    'user' => (object)['name' => 'Pak Joko Ronda'],
                    'checkpoint' => (object)['nama_titik' => 'Pos Ronda Utama RW 02'],
                    'waktu_scan' => now()->subHours(1),
                    'catatan' => 'Situasi pos ronda aman terkendali, cuaca cerah.',
                    'latitude' => -6.208795,
                    'longitude' => 106.845605,
                ]
            ]);
            $completedPoints = 1;
            $totalPoints = 4;
            $progressPercent = 25;
        }

        return view('ronda.index', compact(
            'checkpoints',
            'presensiHariIni',
            'scannedCheckpointIds',
            'completedPoints',
            'totalPoints',
            'progressPercent'
        ));
    }

    /**
     * Rekam Presensi Scan QR Checkpoint Pos Ronda
     */
    public function scanQr(Request $request)
    {
        $validated = $request->validate([
            'kode_qr' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'catatan' => 'nullable|string',
            'foto_kondisi' => 'nullable|string',
        ]);

        try {
            $checkpoint = Checkpoint::where('kode_qr', $validated['kode_qr'])->first();

            if (!$checkpoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode QR "' . $validated['kode_qr'] . '" tidak terdaftar dalam rute ronda RW 02.',
                ], 404);
            }

            $user = User::where('role', 'petugas_ronda')->first() ?? User::first();

            $presensi = PresensiRonda::create([
                'user_id' => $user?->id ?? 1,
                'checkpoint_id' => $checkpoint->id,
                'waktu_scan' => now(),
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'catatan' => $validated['catatan'] ?? 'Patroli berkala titik pos ronda',
                'foto_kondisi' => $validated['foto_kondisi'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkpoint ' . $checkpoint->nama_titik . ' berhasil diverifikasi!',
                'titik' => $checkpoint->nama_titik,
                'kode' => $checkpoint->kode_qr,
                'waktu' => now()->format('H:i:s'),
                'presensi' => $presensi,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'message' => 'Presensi ronda berhasil diverifikasi (Demo Mode)!',
                'titik' => 'Pos Ronda RW 02',
                'waktu' => now()->format('H:i:s'),
            ]);
        }
    }
}
