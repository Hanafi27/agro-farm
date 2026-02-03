<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Note: SQLite doesn't support MODIFY COLUMN for enum changes
        // The divisi column will work with the new values in SQLite
        // Normalisasi data anomali (jika ada baris dengan nilai kosong akibat truncation)
        DB::statement("UPDATE rekapan_laporans SET divisi = 'combined' WHERE divisi = ''");
    }

    public function down(): void
    {
        // Kembalikan ke enum awal (catatan: baris dengan 'combined' akan diubah ke 'peternakan')
        DB::statement("UPDATE rekapan_laporans SET divisi = 'peternakan' WHERE divisi = 'combined'");
        // Note: SQLite doesn't support MODIFY COLUMN for enum changes
    }
};


