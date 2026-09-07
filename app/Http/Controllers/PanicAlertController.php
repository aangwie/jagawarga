<?php

namespace App\Http\Controllers;

use App\Models\PanicAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanicAlertController extends Controller
{
    /**
     * Dapatkan daftar seluruh riwayat kentongan dalam format JSON
     */
    public function listJson()
    {
        PanicAlert::ensureTableExists();

        $alerts = PanicAlert::with('user')->latest()->get()->map(function ($a, $index) {
            return [
                'id' => $a->id,
                'no' => $index + 1,
                'waktu' => $a->waktu_formatted,
                'raw_waktu' => $a->created_at ? $a->created_at->timestamp : 0,
                'kategori' => $a->kategori,
                'kategori_badge' => $a->kategori_badge,
                'kategori_icon' => $a->kategori_icon,
                'catatan' => $a->catatan ?: 'Sinyal bahaya kentongan online',
                'latitude' => (float)$a->latitude,
                'longitude' => (float)$a->longitude,
                'koordinat_label' => number_format((float)$a->latitude, 6) . ', ' . number_format((float)$a->longitude, 6),
                'google_maps_url' => $a->google_maps_url,
                'pelapor' => $a->nama_pelapor,
                'status' => $a->status ?: 'aktif',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $alerts,
        ]);
    }

    /**
     * Dapatkan data kentongan aktif terkini untuk sinkronisasi perangkat PWA
     */
    public function latestActive(Request $request)
    {
        PanicAlert::ensureTableExists();

        // Cari alert dengan status aktif dalam rentang 15 menit terakhir
        $recentThreshold = now()->subMinutes(15);
        $alert = PanicAlert::with('user')
            ->where('status', 'aktif')
            ->where('created_at', '>=', $recentThreshold)
            ->latest('id')
            ->first();

        if (!$alert) {
            return response()->json([
                'success' => true,
                'has_active' => false,
                'alert' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'has_active' => true,
            'alert' => [
                'id' => $alert->id,
                'waktu' => $alert->waktu_formatted,
                'raw_waktu' => $alert->created_at ? $alert->created_at->timestamp : time(),
                'kategori' => $alert->kategori,
                'kategori_badge' => $alert->kategori_badge,
                'kategori_icon' => $alert->kategori_icon,
                'catatan' => $alert->catatan ?: 'Sinyal bahaya kentongan online',
                'latitude' => (float)$alert->latitude,
                'longitude' => (float)$alert->longitude,
                'koordinat_label' => number_format((float)$alert->latitude, 6) . ', ' . number_format((float)$alert->longitude, 6),
                'google_maps_url' => $alert->google_maps_url,
                'pelapor' => $alert->nama_pelapor,
                'status' => $alert->status,
            ],
        ]);
    }

    /**
     * Real-time Server-Sent Events (SSE) stream untuk kentongan darurat
     */
    public function stream(Request $request)
    {
        PanicAlert::ensureTableExists();

        return response()->stream(function () {
            $lastSentId = 0;
            $iterations = 0;

            while (!connection_aborted() && $iterations < 10) {
                $alert = PanicAlert::where('status', 'aktif')
                    ->where('created_at', '>=', now()->subMinutes(15))
                    ->latest('id')
                    ->first();

                if ($alert && $alert->id !== $lastSentId) {
                    $lastSentId = $alert->id;
                    $payload = [
                        'type' => 'PANIC_ALERT',
                        'alert' => [
                            'id' => $alert->id,
                            'waktu' => $alert->waktu_formatted,
                            'kategori' => $alert->kategori,
                            'kategori_icon' => $alert->kategori_icon,
                            'catatan' => $alert->catatan,
                            'latitude' => (float)$alert->latitude,
                            'longitude' => (float)$alert->longitude,
                            'pelapor' => $alert->nama_pelapor,
                            'status' => $alert->status,
                            'timestamp' => $alert->created_at ? $alert->created_at->timestamp : time(),
                        ],
                    ];
                    echo "data: " . json_encode($payload) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                } else {
                    echo ": heartbeat\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }

                $iterations++;
                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Edit / Perbarui Riwayat Kentongan
     * Hak akses: Hanya Admin / Pengurus (role: rt, rw, bhabinkamtibmas)
     * Kolom yang diizinkan diedit: Kategori (Jenis Kejadian) & Catatan (Keterangan)
     */
    public function update(Request $request, $id)
    {
        // Verifikasi hak akses admin / pengurus / nakes
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'rt', 'rw', 'bhabinkamtibmas', 'nakes_puskesmas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Admin / Pengurus RW yang berwenang mengedit riwayat kejadian kentongan.',
            ], 403);
        }

        $validated = $request->validate([
            'kategori' => 'required|string|in:pencurian,kebakaran,medis,lainnya',
            'catatan' => 'required|string|max:1000',
        ]);

        $alert = PanicAlert::findOrFail($id);

        // Hanya perbarui jenis kejadian dan keterangan
        $alert->update([
            'kategori' => $validated['kategori'],
            'catatan' => trim($validated['catatan']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat kentongan #' . $alert->id . ' berhasil diperbarui!',
            'alert' => [
                'id' => $alert->id,
                'kategori' => $alert->kategori,
                'kategori_badge' => $alert->kategori_badge,
                'kategori_icon' => $alert->kategori_icon,
                'catatan' => $alert->catatan,
                'waktu' => $alert->waktu_formatted,
            ],
        ]);
    }

    /**
     * Hapus Rekaman Riwayat Kentongan
     * Hak akses: Hanya Admin / Pengurus (role: rt, rw, bhabinkamtibmas)
     */
    public function destroy(Request $request, $id)
    {
        // Verifikasi hak akses admin / pengurus / nakes
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'rt', 'rw', 'bhabinkamtibmas', 'nakes_puskesmas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Admin / Pengurus RW yang berwenang menghapus riwayat kejadian kentongan.',
            ], 403);
        }

        $alert = PanicAlert::findOrFail($id);
        $alertId = $alert->id;
        $alert->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat kentongan #' . $alertId . ' berhasil dihapus dari sistem.',
        ]);
    }
}
