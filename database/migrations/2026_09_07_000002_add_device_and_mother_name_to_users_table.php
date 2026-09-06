<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nama_ibu')) {
                $table->string('nama_ibu', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'registered_device_id')) {
                $table->string('registered_device_id', 100)->nullable()->after('no_rumah');
            }
            if (!Schema::hasColumn('users', 'device_info')) {
                $table->string('device_info', 255)->nullable()->after('registered_device_id');
            }
            if (!Schema::hasColumn('users', 'device_registered_at')) {
                $table->timestamp('device_registered_at')->nullable()->after('device_info');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nama_ibu',
                'registered_device_id',
                'device_info',
                'device_registered_at',
            ]);
        });
    }
};
