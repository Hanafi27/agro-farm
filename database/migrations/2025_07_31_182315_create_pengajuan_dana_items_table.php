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
        Schema::create('pengajuan_dana_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengajuan_dana_id');
            $table->enum('jenis_kebutuhan', ['operasional', 'gaji', 'konsumsi', 'lainnya']);
            $table->string('nama_kebutuhan');
            $table->decimal('jumlah', 10, 2);
            $table->string('satuan');
            $table->decimal('harga_satuan', 12, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('pengajuan_dana_id')->references('id')->on('pengajuan_danas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_dana_items');
    }
};
