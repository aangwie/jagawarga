<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cctv_lingkungan', function (Blueprint $table) {
            if (!Schema::hasColumn('cctv_lingkungan', 'tipe')) {
                $table->enum('tipe', ['link', 'upload'])->default('link')->after('nama_lokasi');
            }
            if (!Schema::hasColumn('cctv_lingkungan', 'video_path')) {
                $table->string('video_path')->nullable()->after('url_stream');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cctv_lingkungan', function (Blueprint $table) {
            if (Schema::hasColumn('cctv_lingkungan', 'tipe')) {
                $table->dropColumn('tipe');
            }
            if (Schema::hasColumn('cctv_lingkungan', 'video_path')) {
                $table->dropColumn('video_path');
            }
        });
    }
};
