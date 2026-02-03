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
            $table->string('satuan', 50)->default('liter')->after('jumlah_liter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendapatan_susus', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
};
