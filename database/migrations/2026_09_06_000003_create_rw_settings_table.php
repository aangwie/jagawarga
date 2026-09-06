<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('rw_settings')) {
            Schema::create('rw_settings', function (Blueprint $table) {
                $table->id();
                $table->string('rw_id', 5)->default('02');
                $table->string('nama_rw')->default('RW 02 Kelurahan Maju Aman');
                $table->decimal('center_latitude', 10, 8)->default(-6.208800);
                $table->decimal('center_longitude', 11, 8)->default(106.845600);
                $table->integer('panic_radius_meters')->default(300); // Batas radius aktif Panic Button (default 300 meter)
                $table->string('kontak_darurat')->default('0812-3456-7890');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_settings');
    }
};
