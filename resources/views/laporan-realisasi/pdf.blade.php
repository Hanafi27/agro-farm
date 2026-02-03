<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Realisasi - {{ $laporanRealisasi->getDivisiLabel() }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 11px;
            color: #666;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 120px;
        }
        
        .info-value {
            text-align: right;
            flex: 1;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            padding: 5px 10px;
            background-color: #f0f0f0;
            border-left: 4px solid #333;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .table th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            font-size: 10px;
        }
        
        .table .text-center {
            text-align: center;
        }
        
        .table .text-right {
            text-align: right;
        }
        
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .summary-section {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 3px 0;
        }
        
        .summary-label {
            font-weight: bold;
        }
        
        .summary-value {
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-section {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10px;
        }
        
        .page-number {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('asset/logo.png'))) }}" alt="Logo" class="company-logo">
        <div class="company-name">CIWIDEY AGRO FARM</div>
        <div class="company-address">Jl. Raya Ciwidey No. 123, Bandung, Jawa Barat</div>
        <div class="company-address">Telp: (022) 1234567 | Email: info@ciwideyagrofarm.com</div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        Laporan Realisasi Penggunaan Dana<br>
        {{ $laporanRealisasi->getDivisiLabel() }} - {{ $laporanRealisasi->getBulanLabel() }} {{ $laporanRealisasi->tahun }}
    </div>

    <!-- Basic Information -->
    <div class="info-section">
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">Divisi:</span>
                    <span class="info-value">{{ $laporanRealisasi->getDivisiLabel() }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Periode:</span>
                    <span class="info-value">{{ $laporanRealisasi->getBulanLabel() }} {{ $laporanRealisasi->tahun }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Laporan:</span>
                    <span class="info-value">{{ $laporanRealisasi->tanggal ? $laporanRealisasi->tanggal->format('d F Y') : '-' }}</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Dibuat Oleh:</span>
                    <span class="info-value">{{ $laporanRealisasi->submittedBy->name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan Section -->
    <div class="section-title">PENDAPATAN</div>
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Item/Kebutuhan</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Satuan</th>
                <th width="15%">Harga Satuan</th>
                <th width="15%">Total</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanRealisasi->items()->where('kategori', 'pendapatan')->get() as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td class="text-center">{{ $item->getFormattedJumlah() }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">{{ $item->getFormattedHargaSatuan() }}</td>
                <td class="text-right">{{ $item->getFormattedTotalAmount() }}</td>
                <td>
                    @if(str_contains(strtolower($item->keterangan), 'otomatis dari data pendapatan susu'))
                        <span style="color: #2563eb; font-style: italic;">{{ $item->keterangan }}</span>
                    @else
                        @include('laporan-realisasi.partials.attachments-pdf', ['item' => $item])
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data pendapatan</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TOTAL PENDAPATAN:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($laporanRealisasi->total_pendapatan, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Tenaga Kerja & Konsumsi Section -->
    <div class="section-title">TENAGA KERJA & KONSUMSI</div>
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Item/Kebutuhan</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Satuan</th>
                <th width="15%">Harga Satuan</th>
                <th width="15%">Total</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanRealisasi->items()->where('kategori', 'tenaga_konsumsi')->get() as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td class="text-center">{{ $item->getFormattedJumlah() }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">{{ $item->getFormattedHargaSatuan() }}</td>
                <td class="text-right">{{ $item->getFormattedTotalAmount() }}</td>
                <td>@include('laporan-realisasi.partials.attachments-pdf', ['item' => $item])</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data tenaga kerja & konsumsi</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TOTAL TENAGA KERJA & KONSUMSI:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($laporanRealisasi->total_tenaga_konsumsi, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Alat & Bahan Section -->
    <div class="section-title">ALAT & BAHAN</div>
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Item/Kebutuhan</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Satuan</th>
                <th width="15%">Harga Satuan</th>
                <th width="15%">Total</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanRealisasi->items()->where('kategori', 'alat_bahan')->get() as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td class="text-center">{{ $item->getFormattedJumlah() }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">{{ $item->getFormattedHargaSatuan() }}</td>
                <td class="text-right">{{ $item->getFormattedTotalAmount() }}</td>
                <td>@include('laporan-realisasi.partials.attachments-pdf', ['item' => $item])</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data alat & bahan</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TOTAL ALAT & BAHAN:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($laporanRealisasi->total_alat_bahan, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="section-title">RINGKASAN KEUANGAN</div>
        <div class="summary-item">
            <span class="summary-label">Total Pendapatan:</span>
            <span class="summary-value">Rp {{ number_format($laporanRealisasi->total_pendapatan, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Tenaga Kerja & Konsumsi:</span>
            <span class="summary-value">Rp {{ number_format($laporanRealisasi->total_tenaga_konsumsi, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Alat & Bahan:</span>
            <span class="summary-value">Rp {{ number_format($laporanRealisasi->total_alat_bahan, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">TOTAL BIAYA:</span>
            <span class="summary-value">Rp {{ number_format($laporanRealisasi->total_biaya, 0, ',', '.') }}</span>
        </div>
    </div>

    @php
        $allAttachments = $laporanRealisasi->items()->with('attachments')->get()->flatMap(function($i){ return $i->attachments; });
    @endphp
    @if($allAttachments->count())
        <div class="page-number" style="margin-top:30px;"></div>
        <div style="page-break-before: always;"></div>
        <div class="section-title">LAMPIRAN BUKTI TRANSAKSI</div>
        @foreach($laporanRealisasi->items as $item)
            @if($item->attachments && $item->attachments->count())
                <div style="margin:8px 0;font-weight:bold;">{{ $item->nama_item }}</div>
                @foreach($item->attachments as $att)
                    @php 
                        $path = storage_path('app/public/' . $att->path);
                        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        $fileExists = file_exists($path);
                    @endphp
                    @if(in_array($ext, ['jpg','jpeg','png','webp','gif','bmp','svg']) && $fileExists)
                        <div style="margin-bottom:12px;">
                            @php
                                // Use public path for DomPDF
                                $publicPath = public_path('storage/' . $att->path);
                                $fileSize = filesize($path);
                                $isLargeFile = $fileSize > 100000; // > 100KB
                            @endphp
                            
                            @if($isLargeFile)
                                @php
                                    // Use ImageHelper to create optimized image for PDF
                                    $imageData = \App\Helpers\ImageHelper::imageToBase64($path, 400, 400);
                                @endphp
                                
                                @if($imageData)
                                    <img src="{{ $imageData }}" style="max-width:100%;height:auto;border:1px solid #ddd;" />
                                    <div style="font-size:10px;color:#666;">{{ $att->filename }} (optimized - {{ number_format($fileSize / 1024, 1) }} KB)</div>
                                @else
                                    <div style="padding:10px;border:1px solid #e74c3c;background:#fdf2f2;color:#c53030;">
                                        <strong>Error processing image:</strong> {{ $att->filename }}<br>
                                        <small>Ukuran: {{ number_format($fileSize / 1024, 1) }} KB</small><br>
                                        <small>File tidak dapat diproses untuk PDF</small>
                                    </div>
                                @endif
                            @else
                                @php
                                    try {
                                        $imageContent = file_get_contents($path);
                                        $base64Image = base64_encode($imageContent);
                                        $mimeType = $ext === 'svg' ? 'svg+xml' : $ext;
                                        $imageData = "data:image/{$mimeType};base64,{$base64Image}";
                                    } catch (Exception $e) {
                                        $imageData = null;
                                        $error = $e->getMessage();
                                    }
                                @endphp
                                
                                @if($imageData)
                                    <img src="{{ $imageData }}" style="max-width:100%;height:auto;border:1px solid #ddd;" />
                                    <div style="font-size:10px;color:#666;">{{ $att->filename }} ({{ number_format($fileSize / 1024, 1) }} KB)</div>
                                @else
                                    <div style="padding:10px;border:1px solid #ff6b6b;background:#ffe0e0;color:#d63031;">
                                        <strong>Error loading image:</strong> {{ $att->filename }}<br>
                                        <small>Path: {{ $path }}</small><br>
                                        <small>Error: {{ $error ?? 'Unknown error' }}</small>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @elseif($fileExists)
                        <div style="margin-bottom:6px;font-size:11px;">
                            <strong>File:</strong> {{ $att->filename }} ({{ strtoupper($ext) }})<br>
                            <small>Path: {{ $path }}</small>
                        </div>
                    @else
                        <div style="margin-bottom:6px;font-size:11px;color:#e74c3c;">
                            <strong>File not found:</strong> {{ $att->filename }}<br>
                            <small>Expected path: {{ $path }}</small>
                        </div>
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif

    <!-- Keterangan -->
    @if($laporanRealisasi->keterangan)
    <div style="margin-top: 20px;">
        <div class="section-title">KETERANGAN</div>
        <p style="margin: 10px 0; text-align: justify;">{{ $laporanRealisasi->keterangan }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="signature-section">
            <div class="signature-line">Dibuat Oleh</div>
        </div>
        <div class="signature-section">
            <div class="signature-line">Disetujui Oleh</div>
        </div>
    </div>

    <!-- Page Number -->
    <div class="page-number">
        Halaman 1 dari 1
    </div>
</body>
</html> 