<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tambahkan opsi 'rentang' ke enum tipe_periode
        DB::statement("ALTER TABLE `penggajians` MODIFY `tipe_periode` ENUM('harian','bulanan','rentang') NOT NULL");
    }

    public function down(): void
    {
        // Kembalikan ke enum awal tanpa 'rentang'
        DB::statement("ALTER TABLE `penggajians` MODIFY `tipe_periode` ENUM('harian','bulanan') NOT NULL");
    }
};
