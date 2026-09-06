<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrasi ini digantikan oleh tabel checkpoints (2026_09_05_233239_create_checkpoints_table.php)
    }

    public function down(): void
    {
        Schema::dropIfExists('titik_qr');
    }
};