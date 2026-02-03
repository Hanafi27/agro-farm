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
            $table->integer('jumlah_hari')->after('tahun');
            $table->text('keterangan')->nullable()->after('total_gaji');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('tanggal_approval')->nullable()->after('approved_by');
            $table->unsignedBigInteger('paid_by')->nullable()->after('tanggal_approval');
            $table->timestamp('tanggal_pembayaran')->nullable()->after('paid_by');
            
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            $table->dropForeign(['approved_by', 'paid_by']);
            $table->dropColumn([
                'jumlah_hari',
                'keterangan',
                'approved_by',
                'tanggal_approval',
                'paid_by',
                'tanggal_pembayaran'
            ]);
        });
    }
};
