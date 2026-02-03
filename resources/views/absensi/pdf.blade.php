<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi - Ciwidey Agro Farm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 14px;
            color: #666;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .report-info {
            margin-bottom: 20px;
        }
        .report-info table {
            width: 100%;
        }
        .report-info td {
            padding: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .status-hadir {
            background-color: #d4edda;
            color: #155724;
        }
        .status-izin {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-alfa {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary table {
            width: 100%;
            margin-bottom: 0;
        }
        .summary th {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('asset/logo.png'))) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div class="company-name">CIWIDEY AGRO FARM</div>
        <div class="company-address">Jl. Raya Ciwidey No. 123, Bandung, Jawa Barat</div>
    </div>

    <div class="report-title">LAPORAN ABSENSI PEGAWAI</div>

    <div class="report-info">
        <table>
            <tr>
                <td width="100">Periode</td>
                <td width="10">:</td>
                <td>{{ isset($periodeLabel) ? $periodeLabel : (request('tanggal') ? \Carbon\Carbon::parse(request('tanggal'))->format('d F Y') : 'Semua Tanggal') }}</td>
            </tr>
            <tr>
                <td>Pekerja</td>
                <td>:</td>
                <td>{{ request('divisi') ?: 'Semua Pekerja' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td>{{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::now()->format('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>

    @if(isset($rekap))
    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="32%">Nama Pegawai</th>
                <th width="20%">Divisi</th>
                <th width="10%">Hadir</th>
                <th width="10%">Izin</th>
                <th width="10%">Alfa</th>
                <th width="10%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row['pegawai']->nama }}</td>
                <td>{{ $row['pegawai']->divisi }}</td>
                <td style="text-align: center;">{{ $row['hadir'] }}</td>
                <td style="text-align: center;">{{ $row['izin'] }}</td>
                <td style="text-align: center;">{{ $row['alfa'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $row['hadir'] + $row['izin'] + $row['alfa'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data absensi</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @else
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Pegawai</th>
                <th width="15%">Divisi</th>
                <th width="12%">Tanggal</th>
                <th width="10%">Jam Masuk</th>
                <th width="10%">Jam Keluar</th>
                <th width="10%">Status</th>
                <th width="18%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensis as $index => $absensi)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $absensi->pegawai->nama }}</td>
                <td>{{ $absensi->pegawai->divisi }}</td>
                <td style="text-align: center;">{{ $absensi->tanggal ? $absensi->tanggal->format('d/m/Y') : '-' }}</td>
                <td style="text-align: center;">{{ $absensi->jam_masuk ? $absensi->jam_masuk->format('H:i') : '-' }}</td>
                <td style="text-align: center;">{{ $absensi->jam_keluar ? $absensi->jam_keluar->format('H:i') : '-' }}</td>
                <td style="text-align: center;">
                    <span class="status-{{ $absensi->status }}">
                        {{ $absensi->getStatusLabel() }}
                    </span>
                </td>
                <td>{{ $absensi->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data absensi</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <div class="summary">
        <h4 style="margin-bottom: 10px;">Ringkasan Absensi</h4>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($rekap))
                <tr>
                    <td>Hadir</td>
                    <td style="text-align: center;">{{ $rekap->sum('hadir') }}</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td style="text-align: center;">{{ $rekap->sum('izin') }}</td>
                </tr>
                <tr>
                    <td>Alfa</td>
                    <td style="text-align: center;">{{ $rekap->sum('alfa') }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Total Hari Tercatat</td>
                    <td style="text-align: center;">{{ $rekap->sum(function($r){ return $r['hadir'] + $r['izin'] + $r['alfa']; }) }}</td>
                </tr>
                @else
                <tr>
                    <td>Hadir</td>
                    <td style="text-align: center;">{{ $absensis->where('status', 'hadir')->count() }}</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td style="text-align: center;">{{ $absensis->where('status', 'izin')->count() }}</td>
                </tr>
                <tr>
                    <td>Alfa</td>
                    <td style="text-align: center;">{{ $absensis->where('status', 'alfa')->count() }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Total</td>
                    <td style="text-align: center;">{{ $absensis->count() }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
        <p>Laporan ini dibuat secara otomatis oleh sistem Ciwidey Agro Farm</p>
    </div>
</body>
</html> 