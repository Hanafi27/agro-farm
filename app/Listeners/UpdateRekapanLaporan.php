<?php

namespace App\Listeners;

use App\Events\LaporanRealisasiUpdated;
use App\Models\RekapanLaporan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateRekapanLaporan implements ShouldQueue
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
    public function handle(LaporanRealisasiUpdated $event): void
    {
        try {
            $laporanRealisasi = $event->laporanRealisasi;
            $action = $event->action;

            // Handle case where laporan was deleted
            if ($action === 'deleted') {
                Log::info("Updating rekapan for deleted laporan realisasi with action: {$action}");
                
                // Get the month and year from the event data
                $bulan = $laporanRealisasi->bulan;
                $tahun = $laporanRealisasi->tahun;
                $divisi = $laporanRealisasi->divisi;
                $userId = 1; // Default user ID for deleted laporan
            } else {
                Log::info("Updating rekapan for laporan realisasi {$laporanRealisasi->id} with action: {$action}");

                // Get the month and year from the laporan realisasi
                $bulan = $laporanRealisasi->bulan;
                $tahun = $laporanRealisasi->tahun;
                $divisi = $laporanRealisasi->divisi;
                $userId = $laporanRealisasi->submitted_by;
            }

            // Delete existing rekapan for this month/year/divisi
            RekapanLaporan::where('periode_bulan', $bulan)
                ->where('periode_tahun', $tahun)
                ->where('divisi', $divisi)
                ->delete();

            // Check if there are any laporan realisasi for this period/divisi
            $laporanExists = \App\Models\LaporanRealisasi::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('divisi', $divisi)
                ->exists();

            if ($laporanExists) {
                // Regenerate rekapan for this month/year/divisi
                RekapanLaporan::generateFromApprovedLaporan($bulan, $tahun, $divisi, $userId);
            } else {
                Log::info("No laporan realisasi found for {$divisi} {$bulan}/{$tahun}, skipping rekapan generation");
            }

            // Check if there are any laporan realisasi for this period (any divisi)
            $anyLaporanExists = \App\Models\LaporanRealisasi::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if ($anyLaporanExists) {
                // Regenerate combined rekapan for this month/year
                RekapanLaporan::generateCombinedForMonth($bulan, $tahun, $userId);
            } else {
                // Delete combined rekapan if no laporan exists
                RekapanLaporan::where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->where('divisi', 'combined')
                    ->delete();
                Log::info("No laporan realisasi found for {$bulan}/{$tahun}, deleted combined rekapan");
            }

            Log::info("Successfully updated rekapan for {$divisi} {$bulan}/{$tahun}");

        } catch (\Exception $e) {
            Log::error("Failed to update rekapan: " . $e->getMessage());
            throw $e;
        }
    }
}
