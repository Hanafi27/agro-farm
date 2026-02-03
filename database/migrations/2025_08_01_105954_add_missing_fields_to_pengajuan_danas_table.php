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
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            // Add missing fields only if they don't exist
            if (!Schema::hasColumn('pengajuan_danas', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('submitted_by');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('pengajuan_danas', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
                $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('pengajuan_danas', 'alasan_rejection')) {
                $table->text('alasan_rejection')->nullable()->after('rejected_by');
            }
            
            if (!Schema::hasColumn('pengajuan_danas', 'realized_by')) {
                $table->unsignedBigInteger('realized_by')->nullable()->after('alasan_rejection');
                $table->foreign('realized_by')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('pengajuan_danas', 'nominal_diberikan')) {
                $table->decimal('nominal_diberikan', 15, 2)->nullable()->after('realized_by');
            }
            
            // Update status enum if needed (skip for SQLite compatibility)
            // Note: SQLite doesn't support SHOW COLUMNS, so we'll skip this check
            // The status column will be updated in a separate migration if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            // Drop foreign keys
            if (Schema::hasColumn('pengajuan_danas', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            
            if (Schema::hasColumn('pengajuan_danas', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
                $table->dropColumn('rejected_by');
            }
            
            if (Schema::hasColumn('pengajuan_danas', 'alasan_rejection')) {
                $table->dropColumn('alasan_rejection');
            }
            
            if (Schema::hasColumn('pengajuan_danas', 'realized_by')) {
                $table->dropForeign(['realized_by']);
                $table->dropColumn('realized_by');
            }
            
            if (Schema::hasColumn('pengajuan_danas', 'nominal_diberikan')) {
                $table->dropColumn('nominal_diberikan');
            }
            
            // Revert status enum
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending')->change();
        });
    }
};
