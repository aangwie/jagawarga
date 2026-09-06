<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_lingkungan', function (Blueprint $table) {
            $table->id();
            $table->string('rt', 5);
            $table->string('nama_lokasi');
            $table->text('url_stream');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctv_lingkungan');
    }
};