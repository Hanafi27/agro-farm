<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename table to match the expected name in the application
        if (Schema::hasTable('laporan_realisasi_item_atts') && !Schema::hasTable('laporan_realisasi_item_attachments')) {
            Schema::rename('laporan_realisasi_item_atts', 'laporan_realisasi_item_attachments');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('laporan_realisasi_item_attachments') && !Schema::hasTable('laporan_realisasi_item_atts')) {
            Schema::rename('laporan_realisasi_item_attachments', 'laporan_realisasi_item_atts');
        }
    }
};


