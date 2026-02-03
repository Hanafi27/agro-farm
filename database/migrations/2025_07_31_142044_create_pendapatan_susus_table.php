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
        Schema::create('pendapatan_susus', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('pegawai_id')->constrained()->onDelete('cascade');
            $table->decimal('jumlah_liter', 8, 2);
            $table->decimal('harga_per_liter', 8, 2);
            $table->decimal('total_pendapatan', 10, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendapatan_susus');
    }
};
