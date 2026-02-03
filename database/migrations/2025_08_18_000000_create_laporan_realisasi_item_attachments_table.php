<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_realisasi_item_atts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_realisasi_item_id');
            $table->string('path');
            $table->string('filename');
            $table->string('extension', 20)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('laporan_realisasi_item_id', 'lr_item_att_item_fk')
                ->references('id')->on('laporan_realisasi_items')->onDelete('cascade');
            $table->foreign('uploaded_by', 'lr_item_att_user_fk')
                ->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_realisasi_item_atts');
    }
};


