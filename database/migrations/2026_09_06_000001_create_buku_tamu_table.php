<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buku_tamu', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tamu');
            $table->string('nik', 16)->nullable();
            $table->string('no_hp', 20);
            $table->text('alamat_asal');
            $table->text('tujuan_kunjungan');
            $table->string('warga_yang_dikunjungi'); // Nama warga / tuan rumah
            $table->string('rt', 5)->default('01');
            $table->dateTime('tanggal_tiba');
            $table->dateTime('tanggal_keluar')->nullable();
            $table->enum('status', ['menunggu_verifikasi', 'disetujui', 'selesai'])->default('menunggu_verifikasi');
            $table->string('foto_identitas')->nullable(); // Foto KTP/SIM
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_tamu');
    }
};
