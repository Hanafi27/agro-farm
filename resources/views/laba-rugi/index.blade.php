@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="p-6 space-y-6">
    @php
        $routeName = request()->route()->getName();
        $parts = explode('.', $routeName);
        $prefix = $parts[0] ?? 'admin';
        $exportPdfRoute = $prefix.'.laba-rugi.export-pdf';
        $exportExcelRoute = $prefix.'.laba-rugi.export-excel';
        $divisiLabel = $divisi === 'combined' ? 'Semua Divisi' : ucfirst($divisi);
    @endphp
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Laba Rugi</h2>
            <p class="text-gray-600 mt-1">Ringkasan pendapatan dan beban per bulan • Divisi: <span class="font-semibold">{{ $divisiLabel }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route($exportPdfRoute, ['bulan' => $bulan, 'tahun' => $tahun, 'divisi' => request('divisi', $divisi)]) }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
            <a href="{{ route($exportExcelRoute, ['bulan' => $bulan, 'tahun' => $tahun, 'divisi' => request('divisi', $divisi)]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select name="bulan" class="w-full border rounded px-3 py-2">
                    @for($b=1;$b<=12;$b++)
                        <option value="{{ $b }}" {{ (int)request('bulan', $bulan) === $b ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate($tahun, $b, 1)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <input type="number" name="tahun" value="{{ request('tahun', $tahun) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                <select name="divisi" class="w-full border rounded px-3 py-2">
                    <option value="combined" {{ request('divisi', $divisi) === 'combined' ? 'selected' : '' }}>Semua Divisi</option>
                    <option value="peternakan" {{ request('divisi', $divisi) === 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                    <option value="perkebunan" {{ request('divisi', $divisi) === 'perkebunan' ? 'selected' : '' }}>Perkebunan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="bg-agro-green text-white px-4 py-2 rounded hover:bg-green-600">Terapkan</button>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="text-sm text-gray-600">Total Pendapatan</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="text-sm text-gray-600">Total Beban</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">Rp {{ number_format($totalBeban, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-gray-400">
            <div class="text-sm text-gray-600">Laba / (Rugi)</div>
            <div class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Detail Beban (Pengajuan Realized) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detail Beban (Pengajuan Realized)</h3>
        </div>
        <div class="overflow-x-auto">
            @if($pengajuan->count() > 0)
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pengajuan as $p)
                        @foreach($p->items as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $p->tanggal_realisasi ? $p->tanggal_realisasi->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $p->getDivisiLabel() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->nama_kebutuhan }} ({{ $item->getJenisKebutuhanLabel() }})</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="px-6 py-8 text-center">
                <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Beban</h3>
                <p class="text-gray-500">Tidak ada pengajuan dana yang sudah direalisasikan untuk periode ini.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
