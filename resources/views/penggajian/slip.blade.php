<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $penggajian->pegawai->nama }}</title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font-size: 11px;
            line-height: 1.3;
        }
        .slip-container { width: 100%; max-width: 210mm; margin: 0 auto; background: white; position: relative; padding: 15px; box-sizing: border-box; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-logo { width: 60px; height: 60px; margin: 0 auto 8px; display: block; }
        .company-name { font-size: 18px; font-weight: bold; color: #333; margin: 3px 0; text-transform: uppercase; }
        .company-info { font-size: 10px; color: #666; margin: 2px 0; }
        .slip-title { text-align: center; font-size: 16px; font-weight: bold; color: #333; margin: 15px 0; text-transform: uppercase; }
        .employee-info { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .info-section { border: 1px solid #ddd; padding: 10px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 11px; }
        .info-label { font-weight: bold; color: #333; min-width: 70px; }
        .info-value { color: #333; text-align: right; flex: 1; }
        .attendance-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 11px; padding: 3px 0; }
        .attendance-label { font-weight: bold; color: #333; min-width: 70px; }
        .attendance-value { color: #333; text-align: right; flex: 1; font-weight: bold; }
        .attendance-hadir, .attendance-izin, .attendance-alfa { background-color: #f0f0f0; color: #333; }
        .section-header { background-color: #f0f0f0; padding: 8px; font-weight: bold; text-align: center; font-size: 12px; margin: 15px 0 8px 0; border: 1px solid #ddd; }
        .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .salary-table th { background-color: #f0f0f0; padding: 6px; font-weight: bold; text-align: center; font-size: 11px; border: 1px solid #ddd; }
        .salary-table td { padding: 6px; border: 1px solid #ddd; font-size: 11px; }
        .salary-table td:first-child { width: 60%; }
        .salary-table td:last-child { text-align: right; font-weight: bold; width: 40%; }
        .total-row { background-color: #f9f9f9; font-weight: bold; }
        .final-total { background-color: #f0f0f0; padding: 12px; font-weight: bold; text-align: center; font-size: 14px; margin: 15px 0; border: 2px solid #333; }
        .signature-section { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd; }
        .signature-box { text-align: center; width: 45%; }
        .signature-line { border-top: 1px solid #333; margin-top: 30px; padding-top: 3px; font-size: 10px; color: #666; }
        .slip-number { position: absolute; top: 15px; right: 15px; font-size: 9px; color: #666; }
        .print-date { position: absolute; top: 30px; right: 15px; font-size: 9px; color: #666; }
        .footer-info { margin-top: 15px; font-size: 9px; color: #666; text-align: center; }
        @media print { body { -webkit-print-color-adjust: exact; color-adjust: exact; } }
    </style>
</head>
<body>
    <div class="slip-container">
        <!-- Slip Number and Date -->
        <div class="slip-number">
            No: {{ $penggajian->id }}/{{ $penggajian->bulan }}/{{ $penggajian->tahun }}
        </div>
        <div class="print-date">
            Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}
        </div>

        <!-- Header -->
        <div class="header">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('asset/logo.png'))) }}" alt="Logo Perusahaan" class="company-logo">
            <div class="company-name">Ciwidey Agro Farm</div>
            <div class="company-info">Jl. Raya Ciwidey No. 123, Bandung, Jawa Barat</div>
            <div class="company-info">Telp: (022) 1234567 | Email: info@ciwideyagrofarm.com</div>
        </div>

        <!-- Slip Title -->
        <div class="slip-title">Slip Gaji</div>

        <!-- Employee Information -->
        <div class="employee-info">
            <div class="info-section">
                <div class="info-row"><span class="info-label">Nama:</span><span class="info-value">{{ $penggajian->pegawai->nama }}</span></div>
                <div class="info-row"><span class="info-label">Pekerja:</span><span class="info-value">{{ $penggajian->pegawai->divisi }}</span></div>
            </div>
            <div class="info-section">
                <div class="info-row"><span class="info-label">Perusahaan:</span><span class="info-value">Ciwidey Agro Farm</span></div>
                <div class="info-row">
                    <span class="info-label">Periode:</span>
                    <span class="info-value">
                        @if($penggajian->tipe_periode === 'harian')
                            Harian - {{ optional($penggajian->tanggal)->format('d/m/Y') ?? '-' }}
                        @elseif($penggajian->tipe_periode === 'rentang')
                            {{ \Illuminate\Support\Str::of($penggajian->keterangan)->replace('Generated rentang tanggal ', '') }}
                        @else
                            Bulanan - {{ \Carbon\Carbon::createFromDate($penggajian->tahun, $penggajian->bulan, 1)->format('F Y') }}
                        @endif
                    </span>
                </div>
                <div class="attendance-row attendance-hadir"><span class="attendance-label">Hadir:</span><span class="attendance-value">{{ $penggajian->total_hadir }} hari</span></div>
                <div class="attendance-row attendance-izin"><span class="attendance-label">Izin:</span><span class="attendance-value">{{ $penggajian->total_izin }} hari</span></div>
                <div class="attendance-row attendance-alfa"><span class="attendance-label">Alfa:</span><span class="attendance-value">{{ $penggajian->total_alfa }} hari</span></div>
            </div>
        </div>

        <!-- Salary Details -->
        <div class="section-header">RINCIAN GAJI</div>
        <table class="salary-table">
            <thead><tr><th>Keterangan</th><th>Jumlah</th></tr></thead>
            <tbody>
                @if($penggajian->tipe_periode === 'rentang' || $penggajian->tipe_periode === 'harian')
                    <tr><td>Gaji Pokok per Bulan</td><td>Rp {{ number_format($penggajian->gaji_per_bulan, 0, ',', '.') }}</td></tr>
                    <tr><td>Gaji per Hari (Bulan ÷ 30)</td><td>Rp {{ number_format($penggajian->gaji_per_bulan / 30, 0, ',', '.') }}</td></tr>
                    <tr><td>Hadir × Gaji per Hari</td><td>Rp {{ number_format(($penggajian->total_hadir * ($penggajian->gaji_per_bulan / 30)), 0, ',', '.') }}</td></tr>
                    <tr><td>Potongan (Izin + Alfa) × Gaji per Hari</td><td>Rp {{ number_format((($penggajian->total_izin + $penggajian->total_alfa) * ($penggajian->gaji_per_bulan / 30)), 0, ',', '.') }}</td></tr>
                @else
                    <tr><td>Gaji Pokok per Bulan</td><td>Rp {{ number_format($penggajian->gaji_per_bulan, 0, ',', '.') }}</td></tr>
                    <tr><td>Lembur</td><td>Rp 0</td></tr>
                    <tr class="total-row"><td>Total Pendapatan</td><td>Rp {{ number_format($penggajian->gaji_per_bulan, 0, ',', '.') }}</td></tr>
                    <tr><td>Potongan Kehadiran</td><td>Rp {{ number_format($penggajian->potongan, 0, ',', '.') }}</td></tr>
                    <tr><td>Kasbon</td><td>Rp 0</td></tr>
                    <tr class="total-row"><td>Total Potongan</td><td>Rp {{ number_format($penggajian->potongan, 0, ',', '.') }}</td></tr>
                @endif
            </tbody>
        </table>

        <!-- TOTAL GAJI -->
        <div class="final-total"><span>TOTAL GAJI DITERIMA: Rp {{ number_format($penggajian->total_gaji, 0, ',', '.') }}</span></div>

        <!-- Footer Information -->
        <div class="footer-info">
            @if($penggajian->tipe_periode === 'rentang' || $penggajian->tipe_periode === 'harian')
                <p><strong>Keterangan:</strong> {{ $penggajian->keterangan ?: ($penggajian->tipe_periode === 'rentang' ? 'Gaji rentang tanggal' : 'Gaji harian berdasarkan kehadiran') }}</p>
                <p><strong>Perhitungan:</strong> Gaji per hari = Rp {{ number_format($penggajian->gaji_per_bulan / 30, 0, ',', '.') }} | Total = (Hadir × Gaji per hari) − (Izin + Alfa) × Gaji per hari</p>
            @else
                <p><strong>Keterangan:</strong> {{ $penggajian->keterangan ?: 'Gaji bulanan berdasarkan kehadiran' }}</p>
                <p><strong>Perhitungan:</strong> Gaji per hari = Rp {{ number_format($penggajian->gaji_per_bulan / 30, 0, ',', '.') }} | Potongan = (Izin + Alfa) × Gaji per hari</p>
            @endif
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box"><div class="signature-line"><p>Penerima</p></div></div>
            <div class="signature-box"><div class="signature-line"><p>Dibuat Oleh</p></div></div>
        </div>
    </div>
</body>
</html> 