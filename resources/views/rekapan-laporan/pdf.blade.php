<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapan Laporan Realisasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 10px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 120px;
        }
        .summary-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .summary-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
        }
        .summary-grid {
            display: flex;
            justify-content: space-between;
        }
        .summary-item {
            text-align: center;
            flex: 1;
            margin: 0 5px;
        }
        .summary-item .amount {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .summary-item .label {
            font-size: 10px;
            color: #666;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px;
            margin: 15px 0 10px 0;
            font-weight: bold;
            font-size: 14px;
            border-left: 4px solid #333;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .data-table .number {
            text-align: center;
        }
        .data-table .amount {
            text-align: right;
        }
        .data-table .total-row {
            background-color: #fff3cd;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('asset/logo.png'))) }}" alt="Logo" style="width:60px;height:60px;display:block;margin:0 auto 6px;"/>
        <h1>REKAPAN LAPORAN REALISASI BULANAN</h1>
        <p>CIWIDEY AGRO FARM</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Divisi:</td>
            <td>{{ $rekapanLaporan->getDivisiLabel() }}</td>
            
        </tr>
        <tr>
            <td class="label">Periode:</td>
            <td>{{ $rekapanLaporan->getPeriodeLabel() }}</td>
            <td class="label">Dibuat oleh:</td>
            <td>{{ $rekapanLaporan->generatedBy ? $rekapanLaporan->generatedBy->name : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Debit (Pencairan):</td>
            <td>Rp {{ number_format($rekapanLaporan->getDebitAmount(), 0, ',', '.') }}</td>
            <td class="label">Kredit (Pengeluaran):</td>
            <td>Rp {{ number_format($rekapanLaporan->getKreditAmount(), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Saldo:</td>
            <td colspan="3">Rp {{ number_format(abs($rekapanLaporan->getSaldoAmount()), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal dibuat:</td>
            <td>{{ $rekapanLaporan->generated_at ? $rekapanLaporan->generated_at->format('d/m/Y H:i') : '-' }}</td>
            <td class="label">Keterangan:</td>
            <td>{{ $rekapanLaporan->keterangan ?? '-' }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <h3>Ringkasan Keuangan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="amount">Rp {{ number_format($rekapanLaporan->getDebitAmount(), 0, ',', '.') }}</div>
                <div class="label">Debit (Pencairan)</div>
            </div>
            <div class="summary-item">
                <div class="amount">Rp {{ number_format($rekapanLaporan->total_tenaga_konsumsi, 0, ',', '.') }}</div>
                <div class="label">Total Tenaga & Konsumsi</div>
            </div>
            <div class="summary-item">
                <div class="amount">Rp {{ number_format($rekapanLaporan->total_alat_bahan, 0, ',', '.') }}</div>
                <div class="label">Total Alat & Bahan</div>
            </div>
            <div class="summary-item">
                <div class="amount">Rp {{ number_format($rekapanLaporan->getSaldoAmount(), 0, ',', '.') }}</div>
                <div class="label">Saldo</div>
            </div>
        </div>
    </div>

    <!-- Pendapatan Section -->
    <div class="section-title">PENDAPATAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Item/Kebutuhan</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Satuan</th>
                <th width="20%">Harga Satuan</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapanLaporan->items()->where('kategori', 'pendapatan')->get() as $index => $item)
            <tr>
                <td class="number">{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td class="number">{{ $item->jumlah }}</td>
                <td class="number">{{ $item->satuan }}</td>
                <td class="amount">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="amount">{{ $item->getFormattedTotalAmount() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data pendapatan</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>TOTAL PENDAPATAN:</strong></td>
                <td class="amount"><strong>Rp {{ number_format($rekapanLaporan->total_pendapatan, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Tenaga Kerja dan Konsumsi Section -->
    <div class="section-title">TENAGA KERJA DAN KONSUMSI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Item/Kebutuhan</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Satuan</th>
                <th width="20%">Harga Satuan</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapanLaporan->items()->where('kategori', 'tenaga_konsumsi')->get() as $index => $item)
            <tr>
                <td class="number">{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td class="number">{{ $item->jumlah }}</td>
                <td class="number">{{ $item->satuan }}</td>
                <td class="amount">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="amount">{{ $item->getFormattedTotalAmount() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data tenaga & konsumsi</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>TOTAL TENAGA KERJA & KONSUMSI:</strong></td>
                <td class="amount"><strong>Rp {{ number_format($rekapanLaporan->total_tenaga_konsumsi, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Alat dan Bahan Section -->
    <div class="section-title">ALAT DAN BAHAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Item/Kebutuhan</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Satuan</th>
                <th width="20%">Harga Satuan</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapanLaporan->items()->where('kategori', 'alat_bahan')->get() as $index => $item)
            <tr>
                <td class="number">{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td class="number">{{ $item->jumlah }}</td>
                <td class="number">{{ $item->satuan }}</td>
                <td class="amount">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="amount">{{ $item->getFormattedTotalAmount() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data alat & bahan</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>TOTAL ALAT & BAHAN:</strong></td>
                <td class="amount"><strong>Rp {{ number_format($rekapanLaporan->total_alat_bahan, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Total Ringkasan Biaya -->
    <div class="section-title">TOTAL RINGKASAN BIAYA</div>
    <table class="data-table">
        <tbody>
            <tr>
                <td width="80%">Total Tenaga Kerja & Konsumsi</td>
                <td class="amount">Rp {{ number_format($rekapanLaporan->total_tenaga_konsumsi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Alat & Bahan</td>
                <td class="amount">Rp {{ number_format($rekapanLaporan->total_alat_bahan, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL BIAYA</strong></td>
                <td class="amount"><strong>Rp {{ number_format($rekapanLaporan->total_biaya, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Ringkasan Keuangan -->
    <div class="section-title">RINGKASAN KEUANGAN</div>
    <table class="data-table">
        <tbody>
            <tr>
                <td width="80%">Total Pendapatan</td>
                <td class="amount">Rp {{ number_format($rekapanLaporan->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Biaya</td>
                <td class="amount">Rp {{ number_format($rekapanLaporan->total_biaya, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>LABA/RUGI</strong></td>
                <td class="amount"><strong>Rp {{ number_format(abs($rekapanLaporan->total_pendapatan - $rekapanLaporan->total_biaya), 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if(isset($approvedWeekly) && count($approvedWeekly) > 0)
        <div class="page-break"></div>
        <div class="section-title">LAMPIRAN BUKTI TRANSAKSI</div>
        @foreach($approvedWeekly as $weekly)
            @php $attachments = collect($weekly->items)->flatMap(fn($i) => $i->attachments); @endphp
            @if($attachments->count())
                <div style="margin-bottom:10px;font-weight:bold;">Minggu {{ $weekly->minggu }} - {{ $weekly->getDivisiLabel() }} ({{ $weekly->getBulanLabel() }} {{ $weekly->tahun }})</div>
                @foreach($attachments as $att)
                    @php 
                        $path = storage_path('app/public/' . $att->path);
                        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    @endphp
                    @if(in_array($ext, ['jpg','jpeg','png','webp','gif','bmp','svg']))
                        <div style="margin-bottom:12px;">
                            <img src="{{ 'data:image/'.($ext==='svg'?'svg+xml':$ext).';base64,'.base64_encode(file_get_contents($path)) }}" style="max-width:100%;height:auto;" />
                            <div style="font-size:10px;color:#666;">{{ $att->filename }}</div>
                        </div>
                    @else
                        <div style="margin-bottom:6px;font-size:11px;">
                            <strong>File:</strong> {{ $att->filename }} ({{ strtoupper($ext) }}) - lihat asli: {{ public_path('storage/'.$att->path) }}
                        </div>
                    @endif
                @endforeach
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Rekapan ini menggabungkan seluruh data dari laporan realisasi yang sudah di-approve</p>
    </div>
</body>
</html>
