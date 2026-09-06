<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('checkpoints')) {
            Schema::table('checkpoints', function (Blueprint $table) {
                if (!Schema::hasColumn('checkpoints', 'tingkat_kerawanan')) {
                    $table->enum('tingkat_kerawanan', ['rawan', 'sedang', 'aman'])->default('rawan')->after('deskripsi');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('checkpoints')) {
            Schema::table('checkpoints', function (Blueprint $table) {
                if (Schema::hasColumn('checkpoints', 'tingkat_kerawanan')) {
                    $table->dropColumn('tingkat_kerawanan');
                }
            });
        }
    }
};
