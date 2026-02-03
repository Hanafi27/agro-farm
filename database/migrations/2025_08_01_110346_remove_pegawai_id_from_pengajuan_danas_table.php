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
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            // Remove pegawai_id column if it exists
            if (Schema::hasColumn('pengajuan_danas', 'pegawai_id')) {
                $table->dropColumn('pegawai_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            // Add back pegawai_id column
            $table->unsignedBigInteger('pegawai_id')->after('tanggal');
        });
    }
};
