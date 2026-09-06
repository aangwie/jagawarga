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
        if (Schema::hasTable('panic_alerts')) {
            try {
                DB::statement('ALTER TABLE panic_alerts MODIFY user_id BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                // Abaikan jika alter gagal atau driver DB tidak mendukung sintaks ini
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tetap nullable untuk fleksibilitas publik
    }
};
