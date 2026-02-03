<?php

namespace App\Console\Commands;

use App\Models\RekapanLaporan;
use App\Models\RekapanLaporanItem;
use App\Models\LaporanRealisasi;
use Illuminate\Console\Command;

class CleanupOrphanedRekapan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekapan:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned rekapan data that has no corresponding laporan realisasi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting cleanup of orphaned rekapan data...');

        // Get all rekapan data
        $rekapanData = RekapanLaporan::all();
        $cleanedCount = 0;

        foreach ($rekapanData as $rekapan) {
            // Check if corresponding laporan realisasi exists
            $laporanExists = LaporanRealisasi::where('bulan', $rekapan->bulan)
                ->where('tahun', $rekapan->tahun)
                ->where('divisi', $rekapan->divisi)
                ->exists();

            if (!$laporanExists) {
                $this->warn("   ❌ Orphaned rekapan found: {$rekapan->divisi} {$rekapan->bulan}/{$rekapan->tahun}");
                
                // Delete rekapan items first
                RekapanLaporanItem::where('rekapan_laporan_id', $rekapan->id)->delete();
                
                // Delete rekapan
                $rekapan->delete();
                $cleanedCount++;
                
                $this->info("   ✅ Cleaned up orphaned rekapan: {$rekapan->divisi} {$rekapan->bulan}/{$rekapan->tahun}");
            } else {
                $this->line("   ✅ Rekapan is valid: {$rekapan->divisi} {$rekapan->bulan}/{$rekapan->tahun}");
            }
        }

        // Also check for combined rekapan
        $combinedRekapan = RekapanLaporan::where('divisi', 'combined')->get();
        foreach ($combinedRekapan as $rekapan) {
            $anyLaporanExists = LaporanRealisasi::where('bulan', $rekapan->bulan)
                ->where('tahun', $rekapan->tahun)
                ->exists();

            if (!$anyLaporanExists) {
                $this->warn("   ❌ Orphaned combined rekapan found: {$rekapan->bulan}/{$rekapan->tahun}");
                
                // Delete rekapan items first
                RekapanLaporanItem::where('rekapan_laporan_id', $rekapan->id)->delete();
                
                // Delete rekapan
                $rekapan->delete();
                $cleanedCount++;
                
                $this->info("   ✅ Cleaned up orphaned combined rekapan: {$rekapan->bulan}/{$rekapan->tahun}");
            }
        }

        if ($cleanedCount > 0) {
            $this->info("🎉 Cleanup completed! Removed {$cleanedCount} orphaned rekapan records.");
        } else {
            $this->info("✅ No orphaned rekapan data found. All data is consistent.");
        }

        // Show final statistics
        $finalRekapanCount = RekapanLaporan::count();
        $finalLaporanCount = LaporanRealisasi::count();
        
        $this->info("\n📊 Final Statistics:");
        $this->info("   - Laporan Realisasi: {$finalLaporanCount}");
        $this->info("   - Rekapan Laporan: {$finalRekapanCount}");
        
        if ($finalLaporanCount == 0 && $finalRekapanCount == 0) {
            $this->info("   ✅ Data is now consistent - no source data, no rekapan");
        } elseif ($finalLaporanCount > 0 && $finalRekapanCount > 0) {
            $this->info("   ✅ Data is consistent - source data exists, rekapan exists");
        } else {
            $this->warn("   ⚠️ Data inconsistency detected");
        }

        return 0;
    }
}