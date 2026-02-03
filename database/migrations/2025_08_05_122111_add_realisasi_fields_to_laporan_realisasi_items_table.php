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
        Schema::table('laporan_realisasi_items', function (Blueprint $table) {
            $table->decimal('jumlah_realisasi', 10, 2)->nullable()->after('jumlah');
            $table->string('nota')->nullable()->after('jumlah_realisasi');
            $table->text('keterangan_realisasi')->nullable()->after('nota');
            $table->integer('minggu')->nullable()->after('keterangan_realisasi'); // Tambah field minggu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_realisasi_items', function (Blueprint $table) {
            $table->dropColumn(['jumlah_realisasi', 'nota', 'keterangan_realisasi', 'minggu']); // Drop juga field minggu saat rollback
        });
    }
};
