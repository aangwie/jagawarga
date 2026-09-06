<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('buku_tamu')) {
            Schema::table('buku_tamu', function (Blueprint $table) {
                if (!Schema::hasColumn('buku_tamu', 'kewarganegaraan')) {
                    $table->enum('kewarganegaraan', ['WNI', 'WNA'])->default('WNI')->after('nama_tamu');
                }
                if (!Schema::hasColumn('buku_tamu', 'nomor_paspor')) {
                    $table->string('nomor_paspor', 50)->nullable()->after('nik');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('buku_tamu')) {
            Schema::table('buku_tamu', function (Blueprint $table) {
                if (Schema::hasColumn('buku_tamu', 'nomor_paspor')) {
                    $table->dropColumn('nomor_paspor');
                }
                if (Schema::hasColumn('buku_tamu', 'kewarganegaraan')) {
                    $table->dropColumn('kewarganegaraan');
                }
            });
        }
    }
};
