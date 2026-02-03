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
        Schema::create('rekapan_laporans', function (Blueprint $table) {
            $table->id();
            $table->integer('periode_bulan');
            $table->integer('periode_tahun');
            $table->enum('divisi', ['peternakan', 'perkebunan']);
            $table->decimal('total_pendapatan', 15, 2)->default(0);
            $table->decimal('total_tenaga_konsumsi', 15, 2)->default(0);
            $table->decimal('total_alat_bahan', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Add foreign key
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
            
            // Add unique constraint
            $table->unique(['periode_bulan', 'periode_tahun', 'divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekapan_laporans');
    }
};
