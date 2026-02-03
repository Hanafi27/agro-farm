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
        Schema::create('rekapan_laporan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekapan_laporan_id')->constrained('rekapan_laporans')->onDelete('cascade');
            $table->enum('kategori', ['pendapatan', 'tenaga_konsumsi', 'alat_bahan']);
            $table->string('nama_item');
            $table->decimal('jumlah', 10, 2);
            $table->string('satuan', 50);
            $table->decimal('harga_satuan', 15, 2);
            $table->text('keterangan')->nullable();
            $table->integer('minggu')->nullable();
            $table->unsignedBigInteger('laporan_realisasi_id')->nullable();
            $table->timestamps();

            // Add foreign key
            $table->foreign('laporan_realisasi_id')->references('id')->on('laporan_realisasis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekapan_laporan_items');
    }
};
