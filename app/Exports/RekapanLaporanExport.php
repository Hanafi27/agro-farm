<?php

namespace App\Exports;

use App\Models\RekapanLaporan;
use App\Helpers\CurrencyHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RekapanLaporanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithDrawings
{
    protected $rekapanLaporan;

    public function __construct(RekapanLaporan $rekapanLaporan)
    {
        $this->rekapanLaporan = $rekapanLaporan;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Ciwidey Agro Farm');
        $drawing->setPath(public_path('asset/logo.png'));
        $drawing->setHeight(60);
        $drawing->setWidth(60);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    public function collection()
    {
        $debit = (float) $this->rekapanLaporan->getDebitAmount();
        $kredit = (float) $this->rekapanLaporan->total_biaya;
        $saldo = $debit - $kredit;

        return collect([
            // Header
            ['REKAPAN LAPORAN REALISASI BULANAN', '', '', '', '', '', ''],
            ['Divisi: ' . $this->rekapanLaporan->getDivisiLabel(), '', '', '', '', '', ''],
            ['Periode: ' . $this->rekapanLaporan->getPeriodeLabel(), '', '', '', '', '', ''],
            ['Dibuat oleh: ' . ($this->rekapanLaporan->generatedBy ? $this->rekapanLaporan->generatedBy->name : '-'), '', '', '', '', '', ''],
            ['Tanggal dibuat: ' . ($this->rekapanLaporan->generated_at ? $this->rekapanLaporan->generated_at->format('d/m/Y H:i') : '-'), '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            ['RINGKASAN DANA', '', '', '', '', '', ''],
            ['', 'Debit (Pencairan)', '', '', '', CurrencyHelper::formatCurrency($debit), ''],
            ['', 'Kredit (Pengeluaran)', '', '', '', CurrencyHelper::formatCurrency($kredit), ''],
            ['', 'Saldo', '', '', '', CurrencyHelper::formatCurrency($saldo), ''],
            ['', '', '', '', '', '', ''],
            
            // Pendapatan Section
            ['PENDAPATAN', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah', 'Satuan', 'Harga Satuan', 'Total', 'Keterangan'],
        ])->concat(
            $this->rekapanLaporan->items()
                ->where('kategori', 'pendapatan')
                ->get()
                ->map(function($item, $index) {
                    return [
                        $index + 1,
                        $item->nama_item,
                        $item->jumlah,
                        $item->satuan,
                        CurrencyHelper::formatCurrency($item->harga_satuan),
                        CurrencyHelper::formatCurrency($item->getTotalAmount()),
                        $item->keterangan ?? '-'
                    ];
                })
        )->concat([
            ['', 'TOTAL PENDAPATAN', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_pendapatan), ''],
            ['', '', '', '', '', '', ''],
            
            // Tenaga Kerja dan Konsumsi Section
            ['TENAGA KERJA DAN KONSUMSI', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah', 'Satuan', 'Harga Satuan', 'Total', 'Keterangan'],
        ])->concat(
            $this->rekapanLaporan->items()
                ->where('kategori', 'tenaga_konsumsi')
                ->get()
                ->map(function($item, $index) {
                    return [
                        $index + 1,
                        $item->nama_item,
                        $item->jumlah,
                        $item->satuan,
                        CurrencyHelper::formatCurrency($item->harga_satuan),
                        CurrencyHelper::formatCurrency($item->getTotalAmount()),
                        $item->keterangan ?? '-'
                    ];
                })
        )->concat([
            ['', 'TOTAL TENAGA KERJA & KONSUMSI', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_tenaga_konsumsi), ''],
            ['', '', '', '', '', '', ''],
            
            // Alat dan Bahan Section
            ['ALAT DAN BAHAN', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah', 'Satuan', 'Harga Satuan', 'Total', 'Keterangan'],
        ])->concat(
            $this->rekapanLaporan->items()
                ->where('kategori', 'alat_bahan')
                ->get()
                ->map(function($item, $index) {
                    return [
                        $index + 1,
                        $item->nama_item,
                        $item->jumlah,
                        $item->satuan,
                        CurrencyHelper::formatCurrency($item->harga_satuan),
                        CurrencyHelper::formatCurrency($item->getTotalAmount()),
                        $item->keterangan ?? '-'
                    ];
                })
        )->concat([
            ['', 'TOTAL ALAT & BAHAN', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_alat_bahan), ''],
            ['', '', '', '', '', '', ''],
            
            // Total Ringkasan Biaya
            ['TOTAL RINGKASAN BIAYA', '', '', '', '', '', ''],
            ['', 'Total Tenaga Kerja & Konsumsi', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_tenaga_konsumsi), ''],
            ['', 'Total Alat & Bahan', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_alat_bahan), ''],
            ['', 'TOTAL BIAYA', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_biaya), ''],
            ['', '', '', '', '', '', ''],
            
            // Ringkasan Keuangan
            ['RINGKASAN KEUANGAN', '', '', '', '', '', ''],
            ['', 'Total Pendapatan', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_pendapatan), ''],
            ['', 'Total Biaya', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_biaya), ''],
            ['', 'LABA/RUGI', '', '', '', CurrencyHelper::formatCurrency($this->rekapanLaporan->total_pendapatan - $this->rekapanLaporan->total_biaya), ''],
        ]);
    }

    public function headings(): array
    {
        return [];
    }

    public function map($row): array
    {
        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        // Get the highest row and column
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        // Style header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:G6')->getFont()->setBold(true);
        
        // Style section headers (PENDAPATAN, TENAGA KERJA, etc.)
        for ($row = 8; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            if (in_array($cellValue, ['PENDAPATAN', 'TENAGA KERJA DAN KONSUMSI', 'ALAT DAN BAHAN', 'TOTAL RINGKASAN BIAYA', 'RINGKASAN KEUANGAN'])) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
                $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
        
        // Style table headers (No, Nama Item, etc.)
        for ($row = 9; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            if ($cellValue === 'No') {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');
                $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
        
        // Style total rows
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            if (str_contains($cellValue, 'TOTAL')) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');
            }
        }
        
        // Style laba/rugi row
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            if ($cellValue === 'LABA/RUGI') {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5');
            }
        }
        
        // Add borders to all cells with data
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Auto-size columns
        foreach(range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set text alignment for specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jumlah
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Satuan
        $sheet->getStyle('E:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Harga, Total
        
        return $sheet;
    }
}
