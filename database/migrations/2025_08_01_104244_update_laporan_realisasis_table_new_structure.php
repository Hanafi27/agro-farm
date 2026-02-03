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
        Schema::table('laporan_realisasis', function (Blueprint $table) {
            // Drop existing columns
            $table->dropForeign(['pengajuan_dana_id']);
            $table->dropColumn([
                'pengajuan_dana_id',
                'tanggal_realisasi',
                'jumlah_realisasi',
                'bukti_penggunaan'
            ]);

            // Add new columns
            $table->date('tanggal')->after('id');
            $table->enum('divisi', ['peternakan', 'perkebunan'])->after('tanggal');
            $table->integer('minggu')->after('divisi');
            $table->integer('bulan')->after('minggu');
            $table->integer('tahun')->after('bulan');
            $table->enum('status', ['draft', 'pending', 'approved'])->default('draft')->after('tahun');
            $table->unsignedBigInteger('submitted_by')->nullable()->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('submitted_by');
            $table->timestamp('tanggal_approval')->nullable()->after('approved_by');
            $table->decimal('total_pendapatan', 15, 2)->default(0)->after('keterangan');
            $table->decimal('total_tenaga_konsumsi', 15, 2)->default(0)->after('total_pendapatan');
            $table->decimal('total_alat_bahan', 15, 2)->default(0)->after('total_tenaga_konsumsi');
            $table->decimal('total_biaya', 15, 2)->default(0)->after('total_alat_bahan');

            // Add foreign keys
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_realisasis', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'tanggal',
                'divisi',
                'minggu',
                'bulan',
                'tahun',
                'status',
                'submitted_by',
                'approved_by',
                'tanggal_approval',
                'total_pendapatan',
                'total_tenaga_konsumsi',
                'total_alat_bahan',
                'total_biaya'
            ]);

            // Add back old columns
            $table->unsignedBigInteger('pengajuan_dana_id')->after('id');
            $table->date('tanggal_realisasi')->after('pengajuan_dana_id');
            $table->decimal('jumlah_realisasi', 15, 2)->after('tanggal_realisasi');
            $table->text('bukti_penggunaan')->nullable()->after('jumlah_realisasi');

            $table->foreign('pengajuan_dana_id')->references('id')->on('pengajuan_danas')->onDelete('cascade');
        });
    }
};
