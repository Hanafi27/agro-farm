<?php

namespace App\Exports;

use App\Models\PendapatanSusu;
use App\Models\PengajuanDana;
use App\Models\RekapanLaporan;
use App\Helpers\MonthHelper;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LabaRugiExport implements FromArray, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    public function __construct(private int $bulan, private int $tahun, private string $divisi = 'combined') {}

    public function array(): array
    {
        $rows = [];

        $periodeLabel = (MonthHelper::getMonthLabel($this->bulan) ?? $this->bulan) . ' ' . $this->tahun;
        $divisiLabel = $this->divisi === 'combined' ? 'Semua Divisi' : ucfirst($this->divisi);

        // Ringkasan dari Rekapan jika tersedia
        $rekapans = RekapanLaporan::where('periode_bulan', $this->bulan)
            ->where('periode_tahun', $this->tahun)
            ->when($this->divisi !== 'combined', function ($q) { $q->where('divisi', $this->divisi); })
            ->get();

        if ($rekapans->isNotEmpty()) {
            $totalPendapatan = (float) $rekapans->sum(fn ($r) => (float) $r->total_pendapatan);
            $totalBeban      = (float) $rekapans->sum(fn ($r) => (float) $r->total_biaya);
            $labaRugi        = $totalPendapatan - $totalBeban;

            // Header + Ringkasan
            $rows[] = ['Laporan Laba Rugi', 'Periode', $periodeLabel, 'Divisi', $divisiLabel];
            $rows[] = [];
            $rows[] = ['Ringkasan'];
            $rows[] = ['Total Pendapatan', $totalPendapatan];
            $rows[] = ['Total Biaya', $totalBeban];
            $rows[] = ['Laba/Rugi', $labaRugi];
            $rows[] = [];

            // Rincian Per Divisi atau per item sesuai filter
            $rows[] = ['Rincian'];
            $rows[] = ['No', 'Divisi', 'Kategori', 'Nama Item', 'Jumlah', 'Satuan', 'Harga Satuan', 'Total', 'Keterangan'];

            $index = 1;
            foreach ($rekapans as $rekap) {
                $items = $rekap->items()->orderBy('kategori')->get();
                foreach ($items as $item) {
                    $rows[] = [
                        $index++,
                        $rekap->getDivisiLabel(),
                        strtoupper($item->kategori === 'pendapatan' ? 'Pendapatan' : 'Biaya'),
                        $item->nama_item,
                        (float) $item->jumlah,
                        $item->satuan,
                        (float) $item->harga_satuan,
                        (float) $item->getTotalAmount(),
                        $item->keterangan ?? '-',
                    ];
                }
            }

            return $rows;
        }

        // Fallback: hitung langsung jika rekap belum ada
        $pendapatanQuery = PendapatanSusu::whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun);
        if ($this->divisi !== 'combined') {
            $pendapatanQuery->where('kategori', $this->divisi);
        }
        $totalPendapatan = (float) $pendapatanQuery->sum('total_pendapatan');

        $pengajuan = PengajuanDana::where('status', 'realized')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->when($this->divisi !== 'combined', function ($q) { $q->where('divisi', $this->divisi); })
            ->with('items')
            ->get();

        $totalBeban = 0.0;
        foreach ($pengajuan as $p) {
            foreach ($p->items as $item) {
                $totalBeban += (float) $item->jumlah * (float) $item->harga_satuan;
            }
        }
        $labaRugi = (float) $totalPendapatan - (float) $totalBeban;

        $rows[] = ['Laporan Laba Rugi', 'Periode', $periodeLabel, 'Divisi', $divisiLabel];
        $rows[] = [];
        $rows[] = ['Ringkasan'];
        $rows[] = ['Total Pendapatan', $totalPendapatan];
        $rows[] = ['Total Biaya', $totalBeban];
        $rows[] = ['Laba/Rugi', $labaRugi];
        $rows[] = [];
        $rows[] = ['Rincian Belum Tersedia (Rekapan belum dibuat)'];

        return $rows;
    }

    public function title(): string
    {
        return 'Laba Rugi';
    }

    public function styles(Worksheet $sheet)
    {
        // Bold untuk judul dan header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(14);

        // Cari baris header rincian (kolom judul No, Divisi, ...)
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellA = (string) $sheet->getCell('A' . $row)->getValue();
            if ($cellA === 'No') {
                $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()->setHorizontal('center');
                break;
            }
        }

        // Perapihan umum
        $sheet->getDefaultRowDimension()->setRowHeight(18);
        $sheet->getStyle('A:I')->getAlignment()->setVertical('center');
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal('left');
    }

    public function columnFormats(): array
    {
        // Format angka umum dan rupiah
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // nilai ringkasan
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // harga satuan
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // total
        ];
    }
}
