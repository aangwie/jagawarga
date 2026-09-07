<?php

namespace App\Http\Controllers;

use App\Models\PwaDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PwaController extends Controller
{
    /**
     * Daftarkan atau perbarui data perangkat PWA
     */
    public function registerDevice(Request $request): JsonResponse
    {
        PwaDevice::ensureTableExists();

        $validated = $request->validate([
            'device_id' => 'required|string|max:100',
            'is_pwa_installed' => 'nullable|boolean',
            'notification_granted' => 'nullable|boolean',
            'browser_info' => 'nullable|string|max:255',
            'push_subscription' => 'nullable',
        ]);

        $deviceId = $validated['device_id'];
        $pushSub = isset($validated['push_subscription']) 
            ? (is_string($validated['push_subscription']) ? $validated['push_subscription'] : json_encode($validated['push_subscription']))
            : null;

        $device = PwaDevice::firstOrNew(['device_id' => $deviceId]);

        if (Auth::check()) {
            $device->user_id = Auth::id();
        }

        if (isset($validated['is_pwa_installed'])) {
            $device->is_pwa_installed = (bool)$validated['is_pwa_installed'];
        }

        if (isset($validated['notification_granted'])) {
            $device->notification_granted = (bool)$validated['notification_granted'];
        }

        if (!empty($validated['browser_info'])) {
            $device->browser_info = $validated['browser_info'];
        }

        if ($pushSub) {
            $device->push_subscription = $pushSub;
        }

        $device->last_active_at = now();
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Status perangkat PWA berhasil disimpan.',
            'device' => [
                'device_id' => $device->device_id,
                'is_pwa_installed' => (bool)$device->is_pwa_installed,
                'notification_granted' => (bool)$device->notification_granted,
                'last_active_at' => $device->last_active_at ? $device->last_active_at->toIso8601String() : null,
            ],
            'stats' => [
                'total_pwa_installed' => PwaDevice::where('is_pwa_installed', true)->count(),
                'notification_ready' => PwaDevice::where('notification_granted', true)->count(),
            ]
        ]);
    }

    /**
     * Dapatkan status dan statistik perangkat PWA terdaftar
     */
    public function status(): JsonResponse
    {
        PwaDevice::ensureTableExists();

        $totalDevices = PwaDevice::count();
        $installedPwa = PwaDevice::where('is_pwa_installed', true)->count();
        $notifReady = PwaDevice::where('notification_granted', true)->count();
        $readyPwa = PwaDevice::where('is_pwa_installed', true)->where('notification_granted', true)->count();

        return response()->json([
            'success' => true,
            'total_devices' => $totalDevices,
            'installed_pwa' => $installedPwa,
            'notification_ready' => $notifReady,
            'pwa_with_notifications' => $readyPwa,
            'active_in_24h' => PwaDevice::where('last_active_at', '>=', now()->subHours(24))->count(),
        ]);
    }
}
