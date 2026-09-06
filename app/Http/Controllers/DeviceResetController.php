<?php

namespace App\Http\Controllers;

use App\Models\DeviceResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceResetController extends Controller
{
    /**
     * Daftar Permohonan Ganti Perangkat (Admin Panel)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = DeviceResetRequest::with(['user', 'approver'])->latest();

        if ($status !== 'semua' && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $requests = $query->get();

        $stats = [
            'total' => DeviceResetRequest::count(),
            'pending' => DeviceResetRequest::where('status', 'pending')->count(),
            'approved' => DeviceResetRequest::where('status', 'approved')->count(),
            'rejected' => DeviceResetRequest::where('status', 'rejected')->count(),
        ];

        return view('dashboard.device_requests', compact('requests', 'stats', 'status'));
    }

    /**
     * Setujui Permohonan Ganti Perangkat & Reset Device User
     */
    public function approve(Request $request, $id)
    {
        $resetReq = DeviceResetRequest::findOrFail($id);

        $user = $resetReq->user ?: User::where('nik', $resetReq->nik)->first();

        if ($user) {
            // Jika ada new_device_id dari permohonan, langsung tautkan atau kosongkan agar bisa login di device baru
            if ($resetReq->new_device_id) {
                $user->update([
                    'registered_device_id' => $resetReq->new_device_id,
                    'device_info' => $resetReq->new_device_info ?: 'Perangkat Baru Disetujui Admin',
                    'device_registered_at' => now(),
                ]);
            } else {
                $user->resetDevice();
            }
        }

        $resetReq->update([
            'status' => 'approved',
            'admin_notes' => $request->input('admin_notes', 'Disetujui oleh Pengurus RW/RT.'),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Permohonan ganti perangkat untuk ' . ($user->name ?? $resetReq->nik) . ' berhasil disetujui. Perangkat akun telah diperbarui.');
    }

    /**
     * Tolak Permohonan Ganti Perangkat
     */
    public function reject(Request $request, $id)
    {
        $resetReq = DeviceResetRequest::findOrFail($id);

        $resetReq->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes', 'Data verifikasi tidak sesuai atau permohonan ditolak pengurus.'),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('info', 'Permohonan ganti perangkat untuk NIK ' . $resetReq->nik . ' telah ditolak.');
    }

    /**
     * Reset Perangkat User Langsung dari Manajemen Pengguna
     */
    public function resetUserDevice(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->resetDevice();

        return back()->with('success', 'Ikatan perangkat untuk pengguna "' . $user->name . '" berhasil direset. Pengguna dapat mendaftarkan perangkat baru saat memilih peran Warga.');
    }
}
