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
