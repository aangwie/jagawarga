<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RwSetting extends Model
{
    use HasFactory;

    protected $table = 'rw_settings';

    protected $fillable = [
        'rw_id',
        'nama_rw',
        'center_latitude',
        'center_longitude',
        'panic_radius_meters',
        'kontak_darurat',
    ];

    protected $casts = [
        'center_latitude' => 'float',
        'center_longitude' => 'float',
        'panic_radius_meters' => 'integer',
    ];

    /**
     * Dapatkan pengaturan aktif (atau default jika belum ada di database)
     */
    public static function getActiveSetting(): self
    {
        try {
            return static::firstOrCreate(
                ['rw_id' => '02'],
                [
                    'nama_rw' => 'RW 02 Kelurahan Maju Aman',
                    'center_latitude' => -6.208800,
                    'center_longitude' => 106.845600,
                    'panic_radius_meters' => 300,
                    'kontak_darurat' => '0812-3456-7890',
                ]
            );
        } catch (\Throwable $e) {
            $setting = new static();
            $setting->rw_id = '02';
            $setting->nama_rw = 'RW 02 Kelurahan Maju Aman';
            $setting->center_latitude = -6.208800;
            $setting->center_longitude = 106.845600;
            $setting->panic_radius_meters = 300;
            $setting->kontak_darurat = '0812-3456-7890';
            return $setting;
        }
    }

    /**
     * Hitung jarak dua titik koordinat dalam satuan meter menggunakan rumus Haversine
     */
    public static function calculateDistanceMeters($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }
}
