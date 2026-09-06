<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel roles jika belum ada
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50)->unique();
                $table->string('display_name', 100);
                $table->string('description', 255)->nullable();
                $table->string('icon', 20)->default('👤');
                $table->string('badge_color', 50)->default('bg-slate-100 text-slate-800');
                $table->timestamps();
            });

            // Isi role default
            $now = now();
            DB::table('roles')->insert([
                [
                    'name' => 'warga',
                    'display_name' => 'Warga Lingkungan',
                    'description' => 'Akses Panic Button (Kentongan), Pelaporan Kejadian Warga, dan Pengisian Buku Tamu.',
                    'icon' => '🏠',
                    'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'petugas_ronda',
                    'display_name' => 'Petugas Ronda',
                    'description' => 'Akses Portal Poskamling, Scanner QR Titik Patroli, dan Presensi Ronda Malam.',
                    'icon' => '🛡️',
                    'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'rt',
                    'display_name' => 'Ketua RT',
                    'description' => 'Validasi Tamu 2x24 Jam, Monitoring Wilayah RT, dan Pengawasan Jadwal Warga.',
                    'icon' => '🏘️',
                    'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'rw',
                    'display_name' => 'Pengurus RW (Admin)',
                    'description' => 'Command Center Peta Heatmap Kerawanan, Manajemen Pengguna, CCTV, dan Pengaturan Sistem.',
                    'icon' => '🗺️',
                    'badge_color' => 'bg-violet-100 text-violet-800 border-violet-200',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'bhabinkamtibmas',
                    'display_name' => 'Bhabinkamtibmas',
                    'description' => 'Monitoring Keamanan dan Ketertiban Masyarakat (Kamtibmas) Kepolisian di Wilayah RW.',
                    'icon' => '⭐',
                    'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }

        // 2. Buat tabel pivot role_user
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'role_id']);
            });

            // Migrasi role dari tabel users yang sudah ada ke role_user
            $rolesMap = DB::table('roles')->pluck('id', 'name')->toArray();
            $users = DB::table('users')->select('id', 'role')->get();

            $pivotData = [];
            foreach ($users as $user) {
                if ($user->role && isset($rolesMap[$user->role])) {
                    $pivotData[] = [
                        'user_id' => $user->id,
                        'role_id' => $rolesMap[$user->role],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($pivotData)) {
                DB::table('role_user')->insertOrIgnore($pivotData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
