<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing 'pending' status to 'submit'
        DB::table('pengajuan_danas')
            ->where('status', 'pending')
            ->update(['status' => 'submit']);

        // Note: SQLite doesn't support MODIFY COLUMN for enum changes
        // The status column will work with the new values in SQLite
        // For production with MySQL, this would modify the enum
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'submit' back to 'pending'
        DB::table('pengajuan_danas')
            ->where('status', 'submit')
            ->update(['status' => 'pending']);

        // Note: SQLite doesn't support MODIFY COLUMN for enum changes
        // The status column will work with the reverted values in SQLite
    }
};
