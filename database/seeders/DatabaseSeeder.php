<?php

namespace Database\Seeders;

use App\Models\BukuTamu;
use App\Models\CctvLingkungan;
use App\Models\Checkpoint;
use App\Models\JadwalRonda;
use App\Models\LaporanKejadian;
use App\Models\PanicAlert;
use App\Models\PresensiRonda;
use App\Models\RwSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Pastikan Semua Peran (Roles) Tersedia
        $rolesData = [
            ['name' => 'warga', 'display_name' => 'Warga Lingkungan', 'description' => 'Akses Panic Button (Kentongan), Pelaporan Kejadian Warga, dan Pengisian Buku Tamu.', 'icon' => '🏠', 'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            ['name' => 'petugas_ronda', 'display_name' => 'Petugas Ronda', 'description' => 'Akses Portal Poskamling, Scanner QR Titik Patroli, dan Presensi Ronda Malam.', 'icon' => '🛡️', 'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200'],
            ['name' => 'rt', 'display_name' => 'Ketua RT', 'description' => 'Validasi Tamu 2x24 Jam, Monitoring Wilayah RT, dan Pengawasan Jadwal Warga.', 'icon' => '🏘️', 'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200'],
            ['name' => 'rw', 'display_name' => 'Pengurus RW (Admin)', 'description' => 'Command Center Peta Heatmap Kerawanan, Manajemen Pengguna, CCTV, dan Pengaturan Sistem.', 'icon' => '🗺️', 'badge_color' => 'bg-violet-100 text-violet-800 border-violet-200'],
            ['name' => 'bhabinkamtibmas', 'display_name' => 'Bhabinkamtibmas', 'description' => 'Monitoring Keamanan dan Ketertiban Masyarakat (Kamtibmas) Kepolisian di Wilayah RW.', 'icon' => '⭐', 'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
            ['name' => 'nakes_puskesmas', 'display_name' => 'Nakes Puskesmas', 'description' => 'Akses Respon Medis Darurat, Penanganan Pasien Ambulans, dan Pemantauan Kesehatan Warga.', 'icon' => '🩺', 'badge_color' => 'bg-teal-100 text-teal-800 border-teal-200'],
        ];
        foreach ($rolesData as $rd) {
            \App\Models\Role::firstOrCreate(['name' => $rd['name']], $rd);
        }

        // 1. Akun Pengguna Contoh untuk Semua Peran (Role) & Dukungan Multi-Role
        $password = Hash::make('password');

        $warga1 = User::updateOrCreate(
            ['email' => 'warga@jagawarga.local'],
            [
                'name' => 'Budi Santoso',
                'nik' => '3201010101010001',
                'is_nik_verified' => true,
                'nik_verified_at' => now(),
                'phone' => '081234567890',
                'nama_ibu' => 'Siti Aminah',
                'password' => $password,
                'role' => 'warga',
                'rt_id' => '01',
                'rw_id' => '02',
                'alamat' => 'Jl. Kenanga No. 12',
                'no_rumah' => '12',
            ]
        );
        $warga1->syncRoles(['warga']);

        $warga2 = User::updateOrCreate(
            ['email' => 'siti@jagawarga.local'],
            [
                'name' => 'Siti Rahayu',
                'nik' => '3201010101010002',
                'is_nik_verified' => true,
                'nik_verified_at' => now(),
                'phone' => '081234567891',
                'nama_ibu' => 'Rukmini',
                'password' => $password,
                'role' => 'warga',
                'rt_id' => '02',
                'rw_id' => '02',
                'alamat' => 'Jl. Mawar No. 5B',
                'no_rumah' => '5B',
            ]
        );
        $warga2->syncRoles(['warga']);

        // Akun Warga Contoh Belum Terverifikasi NIK (Untuk Pengujian Penolakan Kentongan)
        $wargaUnverified = User::updateOrCreate(
            ['email' => 'unverified@jagawarga.local'],
            [
                'name' => 'Doni Belum Verifikasi',
                'nik' => '3201010101010999',
                'is_nik_verified' => false,
                'nik_verified_at' => null,
                'phone' => '081234567899',
                'nama_ibu' => 'Sumarni',
                'password' => $password,
                'role' => 'warga',
                'rt_id' => '01',
                'rw_id' => '02',
                'alamat' => 'Jl. Kenanga No. 88',
                'no_rumah' => '88',
            ]
        );
        $wargaUnverified->syncRoles(['warga']);

        // Multi-Role: Petugas Ronda sekaligus Warga
        $ronda1 = User::updateOrCreate(
            ['email' => 'ronda@jagawarga.local'],
            [
                'name' => 'Pak Joko Ronda',
                'nik' => '3201010101010003',
                'phone' => '081234567892',
                'nama_ibu' => 'Kartini',
                'password' => $password,
                'role' => 'petugas_ronda',
                'rt_id' => '01',
                'rw_id' => '02',
                'alamat' => 'Jl. Melati No. 03',
                'no_rumah' => '03',
            ]
        );
        $ronda1->syncRoles(['petugas_ronda', 'warga']);

        $ronda2 = User::updateOrCreate(
            ['email' => 'asep@jagawarga.local'],
            [
                'name' => 'Kang Asep Patroli',
                'nik' => '3201010101010004',
                'phone' => '081234567893',
                'nama_ibu' => 'Euis Rohayati',
                'password' => $password,
                'role' => 'petugas_ronda',
                'rt_id' => '02',
                'rw_id' => '02',
                'alamat' => 'Jl. Anggrek No. 08',
                'no_rumah' => '08',
            ]
        );
        $ronda2->syncRoles(['petugas_ronda', 'warga']);

        // Multi-Role: Ketua RT 01 sekaligus Petugas Ronda & Warga
        $rt01 = User::updateOrCreate(
            ['email' => 'rt01@jagawarga.local'],
            [
                'name' => 'Pak Bambang (Ketua RT 01)',
                'nik' => '3201010101010005',
                'phone' => '081234567894',
                'nama_ibu' => 'Sri Wahyuni',
                'password' => $password,
                'role' => 'rt',
                'rt_id' => '01',
                'rw_id' => '02',
                'alamat' => 'Jl. Kenanga No. 01',
                'no_rumah' => '01',
            ]
        );
        $rt01->syncRoles(['rt', 'petugas_ronda', 'warga']);

        $rt02 = User::updateOrCreate(
            ['email' => 'rt02@jagawarga.local'],
            [
                'name' => 'Pak Heri (Ketua RT 02)',
                'nik' => '3201010101010006',
                'phone' => '081234567895',
                'nama_ibu' => 'Endang Sulastri',
                'password' => $password,
                'role' => 'rt',
                'rt_id' => '02',
                'rw_id' => '02',
                'alamat' => 'Jl. Mawar No. 01',
                'no_rumah' => '01',
            ]
        );
        $rt02->syncRoles(['rt', 'warga']);

        // Multi-Role: Pengurus RW 02 sekaligus Ketua RT & Warga
        $rw02 = User::updateOrCreate(
            ['email' => 'rw02@jagawarga.local'],
            [
                'name' => 'Pak Gunawan (Ketua RW 02)',
                'nik' => '3201010101010007',
                'phone' => '081234567896',
                'nama_ibu' => 'Hj. Maryam',
                'password' => $password,
                'role' => 'rw',
                'rt_id' => '01',
                'rw_id' => '02',
                'alamat' => 'Jl. Flamboyan No. 10',
                'no_rumah' => '10',
            ]
        );
        $rw02->syncRoles(['rw', 'rt', 'warga']);

        $bhabin = User::updateOrCreate(
            ['email' => 'bhabin@jagawarga.local'],
            [
                'name' => 'Aiptu Hendro Prasetyo (Bhabinkamtibmas)',
                'nik' => '3201010101010008',
                'phone' => '081234567897',
                'nama_ibu' => 'Suprapti',
                'password' => $password,
                'role' => 'bhabinkamtibmas',
                'rt_id' => '01',
                'rw_id' => '02',
                'alamat' => 'Kantor Kelurahan / Polsek Sektor',
                'no_rumah' => '00',
            ]
        );
        $bhabin->syncRoles(['bhabinkamtibmas', 'warga']);

        // Multi-Role: Nakes Puskesmas sekaligus Warga
        $nakes = User::updateOrCreate(
            ['email' => 'nakes@jagawarga.local'],
            [
                'name' => 'dr. Sarah Amalia (Nakes Puskesmas)',
                'nik' => '3201010101010009',
                'phone' => '081234567898',
                'nama_ibu' => 'Dr. Nurhasanah',
                'password' => $password,
                'role' => 'nakes_puskesmas',
                'rt_id' => '01',
                'rw_id' => '02',
                'alamat' => 'Puskesmas Pembantu RW 02',
                'no_rumah' => '01',
            ]
        );
        $nakes->syncRoles(['nakes_puskesmas', 'warga']);

        // 2. Data Titik Checkpoint Patroli Ronda (QR Code)
        $ckp1 = Checkpoint::updateOrCreate(
            ['kode_qr' => 'JW-CKP-001-POSRW'],
            [
                'rt' => '01',
                'nama_titik' => 'Pos Ronda Utama RW 02',
                'latitude' => -6.208800,
                'longitude' => 106.845600,
                'deskripsi' => 'Pusat kumpul tim ronda, dilengkapi kentongan kayu dan kotak P3K',
                'urutan_patroli' => 1,
            ]
        );

        $ckp2 = Checkpoint::updateOrCreate(
            ['kode_qr' => 'JW-CKP-002-GAPURA'],
            [
                'rt' => '01',
                'nama_titik' => 'Gapura Masuk Gerbang Blok A',
                'latitude' => -6.209500,
                'longitude' => 106.846200,
                'deskripsi' => 'Portal akses kendaraan utama RT 01',
                'urutan_patroli' => 2,
            ]
        );

        $ckp3 = Checkpoint::updateOrCreate(
            ['kode_qr' => 'JW-CKP-003-TAMAN'],
            [
                'rt' => '02',
                'nama_titik' => 'Taman Lingkungan RT 02',
                'latitude' => -6.207900,
                'longitude' => 106.847100,
                'deskripsi' => 'Area bermain terbuka anak dan batas gang perumahan warga',
                'urutan_patroli' => 3,
            ]
        );

        $ckp4 = Checkpoint::updateOrCreate(
            ['kode_qr' => 'JW-CKP-004-GARDU'],
            [
                'rt' => '02',
                'nama_titik' => 'Gardu Trafo PLN & Gang Senggol',
                'latitude' => -6.208300,
                'longitude' => 106.844900,
                'deskripsi' => 'Titik rawan sudut gelap ujung lorong RT 02',
                'urutan_patroli' => 4,
            ]
        );

        // 3. Rekam Presensi Ronda Terkini (Contoh Riwayat)
        PresensiRonda::updateOrCreate(
            ['user_id' => $ronda1->id, 'checkpoint_id' => $ckp1->id],
            [
                'waktu_scan' => now()->subHours(2),
                'latitude' => -6.208795,
                'longitude' => 106.845605,
                'foto_kondisi' => null,
                'catatan' => 'Situasi pos ronda aman terkendali, cuaca cerah',
            ]
        );

        PresensiRonda::updateOrCreate(
            ['user_id' => $ronda1->id, 'checkpoint_id' => $ckp2->id],
            [
                'waktu_scan' => now()->subHours(1)->subMinutes(20),
                'latitude' => -6.209502,
                'longitude' => 106.846198,
                'foto_kondisi' => null,
                'catatan' => 'Portal gapura telah digembok sesuai jam malam pukul 23:00',
            ]
        );

        // 4. CCTV Lingkungan
        CctvLingkungan::updateOrCreate(
            ['nama_lokasi' => 'CCTV 01 - Gapura Masuk Utama RW 02'],
            [
                'rt' => '01',
                'url_stream' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'status' => 'aktif',
            ]
        );

        CctvLingkungan::updateOrCreate(
            ['nama_lokasi' => 'CCTV 02 - Simpang Pos Ronda RW 02'],
            [
                'rt' => '01',
                'url_stream' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'status' => 'aktif',
            ]
        );

        CctvLingkungan::updateOrCreate(
            ['nama_lokasi' => 'CCTV 03 - Taman Terbuka RT 02'],
            [
                'rt' => '02',
                'url_stream' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'status' => 'aktif',
            ]
        );

        // 5. Jadwal Ronda Mingguan
        $jadwalData = [
            ['user_id' => $warga1->id, 'hari' => 'Senin', 'rt_id' => '01'],
            ['user_id' => $ronda1->id, 'hari' => 'Senin', 'rt_id' => '01'],
            ['user_id' => $warga2->id, 'hari' => 'Rabu', 'rt_id' => '02'],
            ['user_id' => $ronda2->id, 'hari' => 'Rabu', 'rt_id' => '02'],
            ['user_id' => $ronda1->id, 'hari' => 'Sabtu', 'rt_id' => '01'],
            ['user_id' => $ronda2->id, 'hari' => 'Sabtu', 'rt_id' => '02'],
            ['user_id' => $rt01->id, 'hari' => 'Minggu', 'rt_id' => '01'],
        ];

        foreach ($jadwalData as $j) {
            JadwalRonda::updateOrCreate(
                ['user_id' => $j['user_id'], 'hari' => $j['hari']],
                ['rt_id' => $j['rt_id']]
            );
        }

        // 6. Laporan Kejadian Warga Contoh
        LaporanKejadian::updateOrCreate(
            ['judul' => 'Lampu Penerangan Jalan Padam di Lorong RT 02'],
            [
                'user_id' => $warga2->id,
                'deskripsi' => 'Lampu PJU nomor tiang 04 padam sejak sore, kondisi jalan sangat gelap dan berpotensi rawan.',
                'foto' => null,
                'latitude' => -6.208150,
                'longitude' => 106.845200,
                'status' => 'diproses',
            ]
        );

        LaporanKejadian::updateOrCreate(
            ['judul' => 'Sepeda Motor Asing Mencurigakan Terparkir Lama'],
            [
                'user_id' => $warga1->id,
                'deskripsi' => 'Ada motor matic hitam tanpa plat nomor ditinggal di depan pagar kosong selama lebih dari 4 jam.',
                'foto' => null,
                'latitude' => -6.209100,
                'longitude' => 106.846000,
                'status' => 'selesai',
            ]
        );

        // 7. Panic Alert (Kentongan Darurat) Riwayat Contoh
        PanicAlert::updateOrCreate(
            ['user_id' => $warga1->id, 'created_at' => now()->subDays(2)],
            [
                'latitude' => -6.208800,
                'longitude' => 106.845600,
                'kategori' => 'pencurian',
                'status' => 'selesai',
                'catatan' => 'Suara mencurigakan di atap belakang, sudah dicek bersama petugas ronda dan dipastikan aman (kucing liar).',
            ]
        );

        // 8. Buku Tamu (Wajib Lapor 2x24 Jam) Contoh
        BukuTamu::updateOrCreate(
            ['nama_tamu' => 'Ahmad Fauzi'],
            [
                'nik' => '3302020202020005',
                'no_hp' => '085712345678',
                'alamat_asal' => 'Jl. Pahlawan No. 45, Purwokerto, Jawa Tengah',
                'tujuan_kunjungan' => 'Silaturahmi keluarga dan menginap selama 2 hari',
                'warga_yang_dikunjungi' => 'Budi Santoso (RT 01)',
                'rt' => '01',
                'tanggal_tiba' => now()->subDay(),
                'tanggal_keluar' => now()->addDay(),
                'status' => 'disetujui',
                'foto_identitas' => null,
                'keterangan' => 'Tamu telah melapor ke ketua RT 01 dan identitas diverifikasi',
            ]
        );

        // 9. Pengaturan Titik Pusat Geofence & Radius Tombol Panic RW 02 (Default 300 meter)
        RwSetting::updateOrCreate(
            ['rw_id' => '02'],
            [
                'nama_rw' => 'RW 02 Kelurahan Maju Aman',
                'center_latitude' => -6.208800,
                'center_longitude' => 106.845600,
                'panic_radius_meters' => 300,
                'kontak_darurat' => '0812-3456-7890',
            ]
        );
    }
}
