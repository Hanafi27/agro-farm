<?php

namespace App\Exports;

use App\Helpers\CurrencyHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class RekapanLaporanBulananExport implements WithMultipleSheets
{
    protected $rekapanLaporansByMonth;
    protected $tahun;

    public function __construct($rekapanLaporansByMonth, $tahun = null)
    {
        $this->rekapanLaporansByMonth = $rekapanLaporansByMonth;
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Create sheets for each month (only months with data)
        foreach ($this->rekapanLaporansByMonth as $month => $rekapanLaporans) {
            foreach ($rekapanLaporans as $rekapanLaporan) {
                // Get month name in Indonesian only
                $monthName = $this->getIndonesianMonthName($month);
                $sheets[] = new RekapanLaporanDetailSheet($rekapanLaporan, $month, $this->tahun, $monthName);
            }
        }
        
        return $sheets;
    }
    
    /**
     * Get Indonesian month name
     */
    private function getIndonesianMonthName($month)
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari', 
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        return $months[$month] ?? 'Bulan ' . $month;
    }
}

class RekapanLaporanDetailSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $rekapanLaporan;
    protected $bulan;
    protected $tahun;
    protected $sheetName;

    public function __construct($rekapanLaporan, $bulan, $tahun, $sheetName = null)
    {
        $this->rekapanLaporan = $rekapanLaporan;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->sheetName = $sheetName;
    }

    public function collection()
    {
        $data = [];
        $saldo = 0;
        $totalDebet = 0;
        $totalKredit = 0;
        
        foreach ($this->rekapanLaporan->items as $item) {
            $itemTotal = $item['jumlah'] * $item['harga_satuan'];
            $isPendapatan = in_array($item['kategori'], ['pendapatan', 'penjualan']);
            
            if ($isPendapatan) {
                $saldo += $itemTotal;
                $totalDebet += $itemTotal;
            } else {
                $saldo -= $itemTotal;
                $totalKredit += $itemTotal;
            }
            
            // Format tanggal berdasarkan minggu
            $minggu = $this->rekapanLaporan->minggu;
            $startDate = Carbon::createFromDate($this->rekapanLaporan->tahun, $this->rekapanLaporan->bulan, 1);
            $weekStart = $startDate->copy()->addWeeks($minggu - 1)->startOfWeek();
            $itemDate = $weekStart->copy()->addDays(rand(0, 6)); // Random day dalam minggu
            
            $data[] = [
                'tanggal' => $itemDate->format('d-M-y'),
                'keterangan' => $isPendapatan ? ucfirst($item['kategori']) . ' ' . $item['nama_item'] : $item['nama_item'],
                'kategori' => strtoupper(str_replace('_', ' ', $item['kategori'])),
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'debet' => $isPendapatan ? $itemTotal : null,
                'kredit' => !$isPendapatan ? $itemTotal : null,
                'saldo' => $saldo,
                'status' => $isPendapatan ? 'Pengajuan' : 'Penyelesaian',
                'is_pendapatan' => $isPendapatan
            ];
        }
        
        // Tambahkan baris total
        $data[] = [
            'tanggal' => '',
            'keterangan' => 'TOTAL',
            'kategori' => '',
            'jumlah' => '',
            'harga_satuan' => '',
            'debet' => $totalDebet,
            'kredit' => $totalKredit,
            'saldo' => $saldo,
            'status' => '-',
            'is_pendapatan' => false,
            'is_total' => true
        ];
        
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'KETERANGAN',
            'KATEGORI',
            'JUMLAH',
            'HARGA SATUAN',
            'Debet',
            'Kredit',
            'Saldo',
            'STATUS'
        ];
    }

    public function map($row): array
    {
        // Jika ini adalah baris total
        if (isset($row['is_total']) && $row['is_total']) {
            return [
                '', // Tanggal kosong
                'TOTAL',
                '', // Kategori kosong
                '', // Jumlah kosong
                '', // Harga satuan kosong
                CurrencyHelper::formatCurrency($row['debet']),
                CurrencyHelper::formatCurrency($row['kredit']),
                CurrencyHelper::formatCurrency($row['saldo']),
                '-'
            ];
        }
        
        return [
            $row['tanggal'],
            $row['keterangan'],
            $row['kategori'],
            number_format($row['jumlah'], 2),
            CurrencyHelper::formatCurrency($row['harga_satuan']),
            $row['debet'] ? CurrencyHelper::formatCurrency($row['debet']) : '-',
            $row['kredit'] ? CurrencyHelper::formatCurrency($row['kredit']) : '-',
            CurrencyHelper::formatCurrency($row['saldo']),
            $row['status']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        $styles = [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]
        ];
        
        // Style untuk baris pendapatan (pengajuan) dan total
        for ($row = 2; $row <= $lastRow; $row++) {
            $status = $sheet->getCell('I' . $row)->getValue();
            $isPendapatan = $status === 'Pengajuan';
            $isTotal = $status === '-';
            
            if ($isPendapatan) {
                $styles[$row] = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA']
                    ],
                    'font' => ['bold' => true]
                ];
            } elseif ($isTotal) {
                $styles[$row] = [
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E9ECEF']
                    ]
                ];
            }
        }
        
        return $styles;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // Tanggal
            'B' => 35, // KETERANGAN
            'C' => 15, // KATEGORI
            'D' => 10, // JUMLAH
            'E' => 15, // HARGA SATUAN
            'F' => 15, // Debet
            'G' => 15, // Kredit
            'H' => 15, // Saldo
            'I' => 12  // STATUS
        ];
    }

    public function title(): string
    {
        if ($this->sheetName) {
            return $this->sheetName;
        }
        return $this->rekapanLaporan->getDivisiLabel();
    }
}
