<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#333; }
        .header { text-align:center; margin-bottom: 16px; padding-bottom:10px; border-bottom:2px solid #333; }
        .header img { width:60px; height:60px; display:block; margin:0 auto 6px; }
        .company { font-weight:bold; font-size:18px; margin:0; }
        .company-info { font-size:11px; margin:4px 0 0 0; }
        .section { margin-top: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('asset/logo.png'))) }}" alt="Logo"/>
        <h1 class="company">CIWIDEY AGRO FARM</h1>
        <div class="company-info">Jl. Raya Ciwidey No. 123, Bandung, Jawa Barat</div>
        <div class="company-info">Telp: (022) 1234567 | Email: info@ciwideyagrofarm.com</div>
    </div>

    <div style="text-align:center; margin-bottom:10px;">
        <strong>LAPORAN LABA RUGI</strong><br/>
        Periode: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}<br/>
        Divisi: {{ ($divisi ?? 'combined') === 'combined' ? 'Semua Divisi' : ucfirst($divisi) }}
    </div>

    <div class="section">
        <table>
            <tr>
                <th>Total Pendapatan</th>
                <td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Debit (Pencairan Dana)</th>
                <td>Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Beban</th>
                <td>Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Laba / (Rugi)</th>
                <td>Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Saldo Kas (Debit - Beban)</th>
                <td>Rp {{ number_format($saldoKas, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h4>Detail Beban (Pengajuan Realized)</h4>
        <table>
            <thead>
                <tr>
                    <th>Tanggal Realisasi</th>
                    <th>Divisi</th>
                    <th>Item</th>
                    <th style="text-align:right">Jumlah</th>
                    <th style="text-align:right">Harga Satuan</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengajuan as $p)
                    @foreach($p->items as $item)
                    <tr>
                        <td>{{ $p->tanggal_realisasi ? $p->tanggal_realisasi->format('d/m/Y') : '-' }}</td>
                        <td>{{ $p->getDivisiLabel() }}</td>
                        <td>{{ $item->nama_kebutuhan }} ({{ $item->getJenisKebutuhanLabel() }})</td>
                        <td style="text-align:right">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan }}</td>
                        <td style="text-align:right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td style="text-align:right">Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
