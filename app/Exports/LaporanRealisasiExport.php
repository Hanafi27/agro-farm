<?php

namespace App\Exports;

use App\Models\LaporanRealisasi;
use App\Helpers\CurrencyHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanRealisasiExport implements WithMultipleSheets
{
    protected $laporanRealisasi;

    public function __construct(LaporanRealisasi $laporanRealisasi)
    {
        $this->laporanRealisasi = $laporanRealisasi;
    }

    public function sheets(): array
    {
        return [
            'Rekap Bulanan' => new RekapBulananSheet($this->laporanRealisasi),
            'Week 1' => new WeekSheet($this->laporanRealisasi, 1),
            'Week 2' => new WeekSheet($this->laporanRealisasi, 2),
            'Week 3' => new WeekSheet($this->laporanRealisasi, 3),
            'Week 4' => new WeekSheet($this->laporanRealisasi, 4),
        ];
    }
}

class RekapBulananSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithDrawings
{
    protected $laporanRealisasi;

    public function __construct(LaporanRealisasi $laporanRealisasi)
    {
        $this->laporanRealisasi = $laporanRealisasi;
    }

    public function title(): string
    {
        return 'Rekap Bulanan';
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
        return collect([
            // Header
            ['LAPORAN REALISASI BULANAN', '', '', '', '', '', ''],
            ['Divisi: ' . $this->laporanRealisasi->getDivisiLabel(), '', '', '', '', '', ''],
            ['Periode: ' . $this->laporanRealisasi->getBulanLabel() . ' ' . $this->laporanRealisasi->tahun, '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            
            // Pendapatan Section
            ['PENDAPATAN', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
        ])->concat(
            $this->laporanRealisasi->items()
                ->where('kategori', 'pendapatan')
                ->get()
                ->map(function($item, $index) {
                    return [
                        $index + 1,
                        $item->nama_item,
                        $item->jumlah,
                        $item->jumlah, // HOK/Porsi same as jumlah for now
                        $item->satuan,
                        CurrencyHelper::formatCurrency($item->harga_satuan),
                        CurrencyHelper::formatCurrency($item->getTotalAmount()),
                        $item->keterangan && str_contains(strtolower($item->keterangan), 'otomatis dari data pendapatan susu') ? $item->keterangan : ($item->keterangan ?? '-')
                    ];
                })
        )->concat([
            ['', 'TOTAL PENDAPATAN', '', '', '', '', CurrencyHelper::formatCurrency($this->laporanRealisasi->total_pendapatan)],
            ['', '', '', '', '', '', ''],
            
            // Tenaga Kerja dan Konsumsi Section
            ['TENAGA KERJA DAN KONSUMSI', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
        ])->concat(
            $this->laporanRealisasi->items()
                ->where('kategori', 'tenaga_konsumsi')
                ->get()
                ->map(function($item, $index) {
                    return [
                        $index + 1,
                        $item->nama_item,
                        $item->jumlah,
                        $item->jumlah,
                        $item->satuan,
                        CurrencyHelper::formatCurrency($item->harga_satuan),
                        CurrencyHelper::formatCurrency($item->getTotalAmount()),
                    ];
                })
        )->concat([
            ['', 'TOTAL TENAGA KERJA & KONSUMSI', '', '', '', '', CurrencyHelper::formatCurrency($this->laporanRealisasi->total_tenaga_konsumsi)],
            ['', '', '', '', '', '', ''],
            
            // Alat dan Bahan Section
            ['ALAT DAN BAHAN', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
        ])->concat(
            $this->laporanRealisasi->items()
                ->where('kategori', 'alat_bahan')
                ->get()
                ->map(function($item, $index) {
                    return [
                        $index + 1,
                        $item->nama_item,
                        $item->jumlah,
                        $item->jumlah,
                        $item->satuan,
                        CurrencyHelper::formatCurrency($item->harga_satuan),
                        CurrencyHelper::formatCurrency($item->getTotalAmount()),
                    ];
                })
        )->concat([
            ['', 'TOTAL ALAT & BAHAN', '', '', '', '', CurrencyHelper::formatCurrency($this->laporanRealisasi->total_alat_bahan)],
            ['', '', '', '', '', '', ''],
            
            // Total Ringkasan Biaya
            ['TOTAL RINGKASAN BIAYA', '', '', '', '', '', ''],
            ['', 'Total Tenaga Kerja & Konsumsi', '', '', '', '', CurrencyHelper::formatCurrency($this->laporanRealisasi->total_tenaga_konsumsi)],
            ['', 'Total Alat & Bahan', '', '', '', '', CurrencyHelper::formatCurrency($this->laporanRealisasi->total_alat_bahan)],
            ['', 'TOTAL BIAYA', '', '', '', '', CurrencyHelper::formatCurrency($this->laporanRealisasi->total_biaya)],
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
        $sheet->getStyle('A2:G2')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        
        // Style section headers (PENDAPATAN, TENAGA KERJA, etc.)
        for ($row = 5; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            if (in_array($cellValue, ['PENDAPATAN', 'TENAGA KERJA DAN KONSUMSI', 'ALAT DAN BAHAN', 'TOTAL RINGKASAN BIAYA'])) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
                $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
        
        // Style table headers (No, Nama Item, etc.)
        for ($row = 6; $row <= $highestRow; $row++) {
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
        
        // Add borders to all cells with data
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Auto-size columns
        foreach(range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set text alignment for specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle('C:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jumlah TK, HOK/Porsi
        $sheet->getStyle('E:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Satuan
        $sheet->getStyle('F:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Harga, Total
        
        return $sheet;
    }
}

class WeekSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithDrawings
{
    protected $laporanRealisasi;
    protected $week;

    public function __construct(LaporanRealisasi $laporanRealisasi, $week)
    {
        $this->laporanRealisasi = $laporanRealisasi;
        $this->week = $week;
    }

    public function title(): string
    {
        return "Week {$this->week}";
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
        // Ambil data item hanya untuk minggu ini
        $itemsPendapatan = $this->laporanRealisasi->items()
            ->where('kategori', 'pendapatan')
            ->where('minggu', $this->week)
            ->get();
        $itemsTenagaKonsumsi = $this->laporanRealisasi->items()
            ->where('kategori', 'tenaga_konsumsi')
            ->where('minggu', $this->week)
            ->get();
        $itemsAlatBahan = $this->laporanRealisasi->items()
            ->where('kategori', 'alat_bahan')
            ->where('minggu', $this->week)
            ->get();

        $hasData = $itemsPendapatan->count() > 0 || $itemsTenagaKonsumsi->count() > 0 || $itemsAlatBahan->count() > 0;
        
        if (!$hasData) {
            // Show empty template for weeks that don't have data yet
            return collect([
                // Header
                ['LAPORAN REALISASI MINGGUAN', '', '', '', '', '', ''],
                ['Divisi: ' . $this->laporanRealisasi->getDivisiLabel(), '', '', '', '', '', ''],
                ['Periode: Week ' . $this->week . ' ' . $this->laporanRealisasi->getBulanLabel() . ' ' . $this->laporanRealisasi->tahun, '', '', '', '', '', ''],
                ['', '', '', '', '', '', ''],
                ['BELUM ADA DATA UNTUK WEEK ' . $this->week, '', '', '', '', '', ''],
                ['', '', '', '', '', '', ''],
                
                // Pendapatan Section
                ['PENDAPATAN', '', '', '', '', '', ''],
                ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
                ['', 'Belum ada data', '', '', '', '', ''],
                ['', 'TOTAL PENDAPATAN', '', '', '', '', 'Rp 0'],
                ['', '', '', '', '', '', ''],
                
                // Tenaga Kerja dan Konsumsi Section
                ['TENAGA KERJA DAN KONSUMSI', '', '', '', '', '', ''],
                ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
                ['', 'Belum ada data', '', '', '', '', ''],
                ['', 'TOTAL TENAGA KERJA & KONSUMSI', '', '', '', '', 'Rp 0'],
                ['', '', '', '', '', '', ''],
                
                // Alat dan Bahan Section
                ['ALAT DAN BAHAN', '', '', '', '', '', ''],
                ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
                ['', 'Belum ada data', '', '', '', '', ''],
                ['', 'TOTAL ALAT & BAHAN', '', '', '', '', 'Rp 0'],
                ['', '', '', '', '', '', ''],
                
                // Total Ringkasan Biaya
                ['TOTAL RINGKASAN BIAYA', '', '', '', '', '', ''],
                ['', 'Total Tenaga Kerja & Konsumsi', '', '', '', '', 'Rp 0'],
                ['', 'Total Alat & Bahan', '', '', '', '', 'Rp 0'],
                ['', 'TOTAL BIAYA', '', '', '', '', 'Rp 0'],
            ]);
        }
        
        // Show actual data for this week
        return collect([
            // Header
            ['LAPORAN REALISASI MINGGUAN', '', '', '', '', '', ''],
            ['Divisi: ' . $this->laporanRealisasi->getDivisiLabel(), '', '', '', '', '', ''],
            ['Periode: Week ' . $this->week . ' ' . $this->laporanRealisasi->getBulanLabel() . ' ' . $this->laporanRealisasi->tahun, '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            
            // Pendapatan Section
            ['PENDAPATAN', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
        ])->concat(
            $itemsPendapatan->map(function($item, $index) {
                return [
                    $index + 1,
                    $item->nama_item,
                    $item->jumlah,
                    $item->jumlah,
                    $item->satuan,
                    CurrencyHelper::formatCurrency($item->harga_satuan),
                    CurrencyHelper::formatCurrency($item->getTotalAmount()),
                ];
            })
        )->concat([
            ['', 'TOTAL PENDAPATAN', '', '', '', '', CurrencyHelper::formatCurrency($itemsPendapatan->sum(function($item){return $item->getTotalAmount();}))],
            ['', '', '', '', '', '', ''],
            
            // Tenaga Kerja dan Konsumsi Section
            ['TENAGA KERJA DAN KONSUMSI', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
        ])->concat(
            $itemsTenagaKonsumsi->map(function($item, $index) {
                return [
                    $index + 1,
                    $item->nama_item,
                    $item->jumlah,
                    $item->jumlah,
                    $item->satuan,
                    CurrencyHelper::formatCurrency($item->harga_satuan),
                    CurrencyHelper::formatCurrency($item->getTotalAmount()),
                ];
            })
        )->concat([
            ['', 'TOTAL TENAGA KERJA & KONSUMSI', '', '', '', '', CurrencyHelper::formatCurrency($itemsTenagaKonsumsi->sum(function($item){return $item->getTotalAmount();}))],
            ['', '', '', '', '', '', ''],
            
            // Alat dan Bahan Section
            ['ALAT DAN BAHAN', '', '', '', '', '', ''],
            ['No', 'Nama Item/Kebutuhan', 'Jumlah TK', 'HOK/Porsi', 'Satuan', 'Harga Satuan', 'Total'],
        ])->concat(
            $itemsAlatBahan->map(function($item, $index) {
                return [
                    $index + 1,
                    $item->nama_item,
                    $item->jumlah,
                    $item->jumlah,
                    $item->satuan,
                    CurrencyHelper::formatCurrency($item->harga_satuan),
                    CurrencyHelper::formatCurrency($item->getTotalAmount()),
                ];
            })
        )->concat([
            ['', 'TOTAL ALAT & BAHAN', '', '', '', '', CurrencyHelper::formatCurrency($itemsAlatBahan->sum(function($item){return $item->getTotalAmount();}))],
            ['', '', '', '', '', '', ''],
            
            // Total Ringkasan Biaya
            ['TOTAL RINGKASAN BIAYA', '', '', '', '', '', ''],
            ['', 'Total Tenaga Kerja & Konsumsi', '', '', '', '', CurrencyHelper::formatCurrency($itemsTenagaKonsumsi->sum(function($item){return $item->getTotalAmount();}))],
            ['', 'Total Alat & Bahan', '', '', '', '', CurrencyHelper::formatCurrency($itemsAlatBahan->sum(function($item){return $item->getTotalAmount();}))],
            ['', 'TOTAL BIAYA', '', '', '', '', CurrencyHelper::formatCurrency($itemsTenagaKonsumsi->sum(function($item){return $item->getTotalAmount();}) + $itemsAlatBahan->sum(function($item){return $item->getTotalAmount();}))],
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
        $sheet->getStyle('A2:G2')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        
        // Style section headers (PENDAPATAN, TENAGA KERJA, etc.)
        for ($row = 5; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            if (in_array($cellValue, ['PENDAPATAN', 'TENAGA KERJA DAN KONSUMSI', 'ALAT DAN BAHAN', 'TOTAL RINGKASAN BIAYA'])) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
                $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
        
        // Style table headers (No, Nama Item, etc.)
        for ($row = 6; $row <= $highestRow; $row++) {
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
        
        // Add borders to all cells with data
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Auto-size columns
        foreach(range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set text alignment for specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle('C:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jumlah TK, HOK/Porsi
        $sheet->getStyle('E:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Satuan
        $sheet->getStyle('F:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Harga, Total
        
        return $sheet;
    }
} 