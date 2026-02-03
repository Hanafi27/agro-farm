<?php

namespace App\Listeners;

use App\Events\PendapatanSusuUpdated;
use App\Models\RekapanLaporan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateRekapanFromPendapatan implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PendapatanSusuUpdated $event): void
    {
        try {
            $pendapatanSusu = $event->pendapatanSusu;
            $action = $event->action;

            Log::info("Updating rekapan from pendapatan susu {$pendapatanSusu->id} with action: {$action}");

            // Get the month and year from the pendapatan susu
            $bulan = $pendapatanSusu->bulan;
            $tahun = $pendapatanSusu->tahun;
            $divisi = $pendapatanSusu->divisi;

            // Delete existing rekapan for this month/year/divisi
            RekapanLaporan::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('divisi', $divisi)
                ->delete();

            // Check if there are any laporan realisasi for this period/divisi
            $laporanExists = \App\Models\LaporanRealisasi::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('divisi', $divisi)
                ->exists();

            if ($laporanExists) {
                // Regenerate rekapan for this month/year/divisi
                RekapanLaporan::generateFromApprovedLaporan($bulan, $tahun, $divisi, 1);
            } else {
                Log::info("No laporan realisasi found for {$divisi} {$bulan}/{$tahun}, skipping rekapan generation");
            }

            // Check if there are any laporan realisasi for this period (any divisi)
            $anyLaporanExists = \App\Models\LaporanRealisasi::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if ($anyLaporanExists) {
                // Regenerate combined rekapan for this month/year
                RekapanLaporan::generateCombinedForMonth($bulan, $tahun, 1);
            } else {
                // Delete combined rekapan if no laporan exists
                RekapanLaporan::where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->where('divisi', 'combined')
                    ->delete();
                Log::info("No laporan realisasi found for {$bulan}/{$tahun}, deleted combined rekapan");
            }

            Log::info("Successfully updated rekapan from pendapatan susu for {$divisi} {$bulan}/{$tahun}");

        } catch (\Exception $e) {
            Log::error("Failed to update rekapan from pendapatan susu: " . $e->getMessage());
            throw $e;
        }
    }
}