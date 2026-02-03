<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Http\Request;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithDrawings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Absensi::with(['pegawai.user']);
        
        // Apply filters
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->whereHas('pegawai', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        
        if ($this->request->filled('divisi')) {
            $query->whereHas('pegawai', function($q) {
                $q->where('divisi', $this->request->divisi);
            });
        }
        
        if ($this->request->filled('tanggal')) {
            $query->whereDate('tanggal', $this->request->tanggal);
        }
        
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        
        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'Divisi',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Keterangan',
        ];
    }

    public function map($absensi): array
    {
        static $no = 1;
        
        return [
            $no++,
            $absensi->pegawai->nama,
            $absensi->pegawai->divisi,
                            $absensi->tanggal ? $absensi->tanggal->format('d/m/Y') : '-',
            $absensi->jam_masuk ? $absensi->jam_masuk->format('H:i') : '-',
            $absensi->jam_keluar ? $absensi->jam_keluar->format('H:i') : '-',
            $absensi->getStatusLabel(),
            $absensi->keterangan ?? '-',
        ];
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

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }

    public function title(): string
    {
        return 'Absensi ' . date('F Y');
    }
} 