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
            $table->unsignedBigInteger('approved_by')->nullable()->after('tanggal_approval');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
            $table->text('alasan_rejection')->nullable()->after('rejected_by');
            $table->date('tanggal_realisasi')->nullable()->after('alasan_rejection');
            $table->decimal('nominal_diberikan', 15, 2)->nullable()->after('tanggal_realisasi');
            $table->unsignedBigInteger('realized_by')->nullable()->after('nominal_diberikan');
            
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('realized_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            $table->dropForeign(['approved_by', 'rejected_by', 'realized_by']);
            $table->dropColumn([
                'approved_by',
                'rejected_by',
                'alasan_rejection',
                'tanggal_realisasi',
                'nominal_diberikan',
                'realized_by'
            ]);
        });
    }
};
