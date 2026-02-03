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
            // Drop old columns if they exist
            if (Schema::hasColumn('pengajuan_danas', 'pegawai_id')) {
                $table->dropForeign(['pegawai_id']);
                $table->dropColumn('pegawai_id');
            }
            if (Schema::hasColumn('pengajuan_danas', 'nama_pengajuan')) {
                $table->dropColumn('nama_pengajuan');
            }
            if (Schema::hasColumn('pengajuan_danas', 'tanggal_pengajuan')) {
                $table->dropColumn('tanggal_pengajuan');
            }
            if (Schema::hasColumn('pengajuan_danas', 'jumlah_dana')) {
                $table->dropColumn('jumlah_dana');
            }
            if (Schema::hasColumn('pengajuan_danas', 'tujuan')) {
                $table->dropColumn('tujuan');
            }
            
            // Add new columns
            if (!Schema::hasColumn('pengajuan_danas', 'tanggal')) {
                $table->date('tanggal')->after('id');
            }
            if (!Schema::hasColumn('pengajuan_danas', 'divisi')) {
                $table->enum('divisi', ['peternakan', 'perkebunan'])->after('tanggal');
            }
            if (!Schema::hasColumn('pengajuan_danas', 'minggu')) {
                $table->integer('minggu')->after('divisi');
            }
            if (!Schema::hasColumn('pengajuan_danas', 'bulan')) {
                $table->integer('bulan')->after('minggu');
            }
            if (!Schema::hasColumn('pengajuan_danas', 'tahun')) {
                $table->integer('tahun')->after('bulan');
            }
            if (!Schema::hasColumn('pengajuan_danas', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->after('tahun');
                $table->foreign('submitted_by')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            // Remove new columns
            if (Schema::hasColumn('pengajuan_danas', 'tanggal')) {
                $table->dropColumn('tanggal');
            }
            if (Schema::hasColumn('pengajuan_danas', 'divisi')) {
                $table->dropColumn('divisi');
            }
            if (Schema::hasColumn('pengajuan_danas', 'minggu')) {
                $table->dropColumn('minggu');
            }
            if (Schema::hasColumn('pengajuan_danas', 'bulan')) {
                $table->dropColumn('bulan');
            }
            if (Schema::hasColumn('pengajuan_danas', 'tahun')) {
                $table->dropColumn('tahun');
            }
            if (Schema::hasColumn('pengajuan_danas', 'submitted_by')) {
                $table->dropForeign(['submitted_by']);
                $table->dropColumn('submitted_by');
            }
            
            // Add back old columns
            $table->unsignedBigInteger('pegawai_id')->after('id');
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
            $table->string('nama_pengajuan')->after('pegawai_id');
            $table->date('tanggal_pengajuan')->after('nama_pengajuan');
            $table->decimal('jumlah_dana', 12, 2)->after('tanggal_pengajuan');
            $table->text('tujuan')->after('jumlah_dana');
        });
    }
};
