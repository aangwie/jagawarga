<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_nik_verified')) {
                $table->boolean('is_nik_verified')->default(true)->after('nik');
            }
            if (!Schema::hasColumn('users', 'nik_verified_at')) {
                $table->timestamp('nik_verified_at')->nullable()->after('is_nik_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nik_verified_at')) {
                $table->dropColumn('nik_verified_at');
            }
            if (Schema::hasColumn('users', 'is_nik_verified')) {
                $table->dropColumn('is_nik_verified');
            }
        });
    }
};
