<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendapatanSusu;
use App\Models\PengajuanDana;
use App\Models\RekapanLaporan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LabaRugiExport;
use Maatwebsite\Excel\Facades\Excel;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) ($request->get('bulan') ?: now()->month);
        $tahun = (int) ($request->get('tahun') ?: now()->year);
        $divisi = $request->get('divisi', 'combined');

        [$totalPendapatan, $totalBeban, $labaRugi, $pengajuan, $totalDebit, $saldoKas] = $this->computeData($bulan, $tahun, $divisi);

        return view('laba-rugi.index', compact('bulan','tahun','divisi','totalPendapatan','totalBeban','labaRugi','pengajuan','totalDebit','saldoKas'));
    }

    public function exportPdf(Request $request)
    {
        $bulan = (int) ($request->get('bulan') ?: now()->month);
        $tahun = (int) ($request->get('tahun') ?: now()->year);
        $divisi = $request->get('divisi', 'combined');
        [$totalPendapatan, $totalBeban, $labaRugi, $pengajuan, $totalDebit, $saldoKas] = $this->computeData($bulan, $tahun, $divisi);

        $pdf = Pdf::loadView('laba-rugi.pdf', compact('bulan','tahun','divisi','totalPendapatan','totalBeban','labaRugi','pengajuan','totalDebit','saldoKas'));
        return $pdf->download('laba-rugi-'.$bulan.'-'.$tahun.'-'.$divisi.'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $bulan = (int) ($request->get('bulan') ?: now()->month);
        $tahun = (int) ($request->get('tahun') ?: now()->year);
        $divisi = $request->get('divisi', 'combined');
        return Excel::download(new LabaRugiExport($bulan, $tahun, $divisi), 'laba-rugi-'.$bulan.'-'.$tahun.'-'.$divisi.'.xlsx');
    }

    private function computeData(int $bulan, int $tahun, string $divisi = 'combined'): array
    {
        // 1) Coba gunakan angka dari Rekapan (lebih konsisten untuk laporan)
        $rekapans = RekapanLaporan::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->when($divisi !== 'combined', function ($q) use ($divisi) { $q->where('divisi', $divisi); })
            ->get();

        if ($rekapans->isNotEmpty()) {
            // Calculate real-time from source data instead of stored values
            $totalPendapatan = (float) RekapanLaporan::computePendapatan($bulan, $tahun, $divisi);
            $totalBeban = (float) RekapanLaporan::computeKredit($bulan, $tahun, $divisi);
            $labaRugi = $totalPendapatan - $totalBeban;

            // Tetap ambil pengajuan realized untuk ditampilkan sebagai detail (opsional)
            $pengajuan = PengajuanDana::where('status', 'realized')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->when($divisi !== 'combined', function ($q) use ($divisi) { $q->where('divisi', $divisi); })
                ->with('items')
                ->get();

            // Debit = total pencairan dana (sum items realized)
            $totalDebit = 0.0;
            foreach ($pengajuan as $p) {
                foreach ($p->items as $item) {
                    $totalDebit += (float) $item->jumlah * (float) $item->harga_satuan;
                }
            }
            $saldoKas = (float) ($totalDebit - $totalBeban);

            return [$totalPendapatan, $totalBeban, $labaRugi, $pengajuan, $totalDebit, $saldoKas];
        }

        // 1b) Jika rekap belum ada, coba generate otomatis dari laporan realisasi approved
        try {
            $userId = auth()->id() ?: 1;
            if ($divisi === 'combined') {
                RekapanLaporan::generateCombinedForMonth($bulan, $tahun, (int) $userId);
            } else {
                RekapanLaporan::generateFromApprovedLaporan($bulan, $tahun, $divisi, (int) $userId);
            }
            $rekapans = RekapanLaporan::where('periode_bulan', $bulan)
                ->where('periode_tahun', $tahun)
                ->when($divisi !== 'combined', function ($q) use ($divisi) { $q->where('divisi', $divisi); })
                ->get();
            if ($rekapans->isNotEmpty()) {
                $totalPendapatan = (float) RekapanLaporan::computePendapatan($bulan, $tahun, $divisi);
                $totalBeban = (float) RekapanLaporan::computeKredit($bulan, $tahun, $divisi);
                $labaRugi = $totalPendapatan - $totalBeban;
                $pengajuan = PengajuanDana::where('status', 'realized')
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->when($divisi !== 'combined', function ($q) use ($divisi) { $q->where('divisi', $divisi); })
                    ->with('items')
                    ->get();
                $totalDebit = 0.0;
                foreach ($pengajuan as $p) {
                    foreach ($p->items as $item) {
                        $totalDebit += (float) $item->jumlah * (float) $item->harga_satuan;
                    }
                }
                $saldoKas = (float) ($totalDebit - $totalBeban);
                return [$totalPendapatan, $totalBeban, $labaRugi, $pengajuan, $totalDebit, $saldoKas];
            }
        } catch (\Throwable $e) {
            // Abaikan jika tidak ada laporan approved; akan fallback hitung langsung di bawah
        }

        // 2) Fallback: hitung langsung dari sumber data bila rekapan belum ada
        $totalPendapatan = (float) RekapanLaporan::computePendapatan($bulan, $tahun, $divisi);

        $pengajuan = PengajuanDana::where('status', 'realized')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->when($divisi !== 'combined', function ($q) use ($divisi) { $q->where('divisi', $divisi); })
            ->with('items')
            ->get();

        // Calculate total beban from laporan realisasi (real-time)
        $totalBeban = (float) RekapanLaporan::computeKredit($bulan, $tahun, $divisi);
        $labaRugi = (float) $totalPendapatan - (float) $totalBeban;
        $totalDebit = 0.0;
        foreach ($pengajuan as $p) {
            foreach ($p->items as $item) {
                $totalDebit += (float) $item->jumlah * (float) $item->harga_satuan;
            }
        }
        $saldoKas = (float) ($totalDebit - $totalBeban);
        return [$totalPendapatan, $totalBeban, $labaRugi, $pengajuan, $totalDebit, $saldoKas];
    }
}
