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
        Schema::table('pendapatan_susus', function (Blueprint $table) {
            // Remove old pegawai_id field
            if (Schema::hasColumn('pendapatan_susus', 'pegawai_id')) {
                $table->dropForeign(['pegawai_id']);
                $table->dropColumn('pegawai_id');
            }
            
            // Add new fields
            if (!Schema::hasColumn('pendapatan_susus', 'kategori')) {
                $table->enum('kategori', ['perkebunan', 'peternakan'])->after('tanggal');
            }
            if (!Schema::hasColumn('pendapatan_susus', 'jenis_produk')) {
                $table->enum('jenis_produk', ['teh', 'susu_kambing', 'susu_sapi'])->after('kategori');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendapatan_susus', function (Blueprint $table) {
            // Remove new fields
            if (Schema::hasColumn('pendapatan_susus', 'kategori')) {
                $table->dropColumn('kategori');
            }
            if (Schema::hasColumn('pendapatan_susus', 'jenis_produk')) {
                $table->dropColumn('jenis_produk');
            }
            
            // Add back old field
            $table->unsignedBigInteger('pegawai_id')->after('tanggal');
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
        });
    }
};
