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
        Schema::table('penggajians', function (Blueprint $table) {
            // Remove old fields if they exist
            if (Schema::hasColumn('penggajians', 'jumlah_hari')) {
                $table->dropColumn('jumlah_hari');
            }
            if (Schema::hasColumn('penggajians', 'gaji_pokok')) {
                $table->dropColumn('gaji_pokok');
            }
            if (Schema::hasColumn('penggajians', 'tunjangan')) {
                $table->dropColumn('tunjangan');
            }
            
            // Add new fields if they don't exist
            if (!Schema::hasColumn('penggajians', 'gaji_per_bulan')) {
                $table->decimal('gaji_per_bulan', 15, 2)->after('tahun');
            }
            if (!Schema::hasColumn('penggajians', 'gaji_per_minggu')) {
                $table->decimal('gaji_per_minggu', 15, 2)->after('gaji_per_bulan');
            }
            if (!Schema::hasColumn('penggajians', 'total_hadir')) {
                $table->integer('total_hadir')->after('gaji_per_minggu');
            }
            if (!Schema::hasColumn('penggajians', 'total_izin')) {
                $table->integer('total_izin')->after('total_hadir');
            }
            if (!Schema::hasColumn('penggajians', 'total_alfa')) {
                $table->integer('total_alfa')->after('total_izin');
            }
            // Note: potongan column already exists from previous migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            // Remove new fields
            if (Schema::hasColumn('penggajians', 'gaji_per_bulan')) {
                $table->dropColumn('gaji_per_bulan');
            }
            if (Schema::hasColumn('penggajians', 'gaji_per_minggu')) {
                $table->dropColumn('gaji_per_minggu');
            }
            if (Schema::hasColumn('penggajians', 'total_hadir')) {
                $table->dropColumn('total_hadir');
            }
            if (Schema::hasColumn('penggajians', 'total_izin')) {
                $table->dropColumn('total_izin');
            }
            if (Schema::hasColumn('penggajians', 'total_alfa')) {
                $table->dropColumn('total_alfa');
            }
            
            // Add back old fields
            $table->integer('jumlah_hari')->after('tahun');
            $table->decimal('gaji_pokok', 15, 2)->after('jumlah_hari');
            $table->decimal('tunjangan', 15, 2)->after('gaji_pokok');
        });
    }
};
