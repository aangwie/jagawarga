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
        // 1. Ubah kolom role di tabel users menjadi string(50) agar fleksibel
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('warga')->change();
        });

        // 2. Tambahkan role nakes_puskesmas ke tabel roles
        $exists = DB::table('roles')->where('name', 'nakes_puskesmas')->exists();
        if (!$exists) {
            DB::table('roles')->insert([
                'name' => 'nakes_puskesmas',
                'display_name' => 'Nakes Puskesmas',
                'description' => 'Akses Respon Medis Darurat, Penanganan Pasien Ambulans, dan Pemantauan Kesehatan Warga.',
                'icon' => '🩺',
                'badge_color' => 'bg-teal-100 text-teal-800 border-teal-200',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('name', 'nakes_puskesmas')->delete();
    }
};
