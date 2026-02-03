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
        Schema::create('laporan_realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_dana_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_realisasi');
            $table->decimal('jumlah_realisasi', 12, 2);
            $table->string('bukti_penggunaan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_realisasis');
    }
};
