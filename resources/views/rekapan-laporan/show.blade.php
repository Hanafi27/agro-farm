@extends('layouts.app')

@section('title', 'Detail Rekapan - Ciwidey Agro Farm')

@section('page-title', 'Detail Rekapan Laporan')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">{{ $rekapan->getPeriodeLabel() }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        {{ $rekapan->divisi === 'combined' ? 'bg-purple-100 text-purple-800' : 
                           ($rekapan->divisi === 'peternakan' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $rekapan->getDivisiLabel() }}
                    </span>
                </p>
            </div>
            
        </div>

        <!-- Summary Cards (Debit/Kredit/Saldo) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-arrow-down text-green-600 text-2xl mr-3"></i>
                    <div>
                        <div class="text-sm text-green-600 font-medium">Debit (Pencairan)</div>
                        <div class="text-xl font-bold text-green-800">Rp {{ number_format($rekapan->getDebitAmount(), 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-arrow-up text-red-600 text-2xl mr-3"></i>
                    <div>
                        <div class="text-sm text-red-600 font-medium">Kredit (Pengeluaran)</div>
                        <div class="text-xl font-bold text-red-800">Rp {{ number_format($rekapan->getKreditAmount(), 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-balance-scale text-blue-600 text-2xl mr-3"></i>
                    <div>
                        <div class="text-sm text-blue-600 font-medium">Saldo</div>
                        <div class="text-xl font-bold text-gray-800">
                            Rp {{ number_format(abs($rekapan->getSaldoAmount()), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <strong>Dibuat oleh:</strong> {{ $rekapan->generatedBy->name ?? 'System' }}
                </div>
                <div>
                    <strong>Tanggal dibuat:</strong> {{ $rekapan->generated_at ? $rekapan->generated_at->format('d/m/Y H:i') : '-' }}
                </div>
            </div>
            @if($rekapan->keterangan)
            <div class="mt-2">
                <strong>Keterangan:</strong> {{ $rekapan->keterangan }}
            </div>
            @endif
        </div>
    </div>

    <!-- Items Detail - Separated by Type -->
    @if($rekapan->items->count() > 0)
    <div class="space-y-6">
        @php
            $pendapatanItems = $rekapan->items->where('kategori', 'pendapatan');
            $biayaItems = $rekapan->items->whereIn('kategori', ['tenaga_konsumsi', 'alat_bahan']);
        @endphp

        <!-- PENDAPATAN SECTION -->
        @if($pendapatanItems->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-arrow-up text-green-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-green-800">PENDAPATAN</h3>
                    <p class="text-sm text-green-600">Total: Rp {{ number_format($rekapan->total_pendapatan, 0, ',', '.') }}</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-green-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nama Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Harga Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendapatanItems as $item)
                        <tr class="hover:bg-green-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->nama_item }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->satuan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-700">
                                Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $item->keterangan ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- BIAYA SECTION -->
        @if($biayaItems->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-arrow-down text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-red-800">BIAYA OPERASIONAL</h3>
                    <p class="text-sm text-red-600">Total: Rp {{ number_format($rekapan->total_biaya, 0, ',', '.') }}</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Nama Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Harga Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($biayaItems->groupBy('kategori') as $kategori => $items)
                            @foreach($items as $index => $item)
                            <tr class="hover:bg-red-50">
                                @if($index === 0)
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" rowspan="{{ $items->count() }}">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $kategori === 'tenaga_konsumsi' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ ucwords(str_replace('_', ' ', $kategori)) }}
                                    </span>
                                </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->nama_item }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->satuan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-700">
                                    Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center py-8">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Item</h3>
            <p class="text-gray-500">Rekapan ini belum memiliki item detail.</p>
        </div>
    </div>
    @endif

    <!-- Back Button -->
    <div class="mt-6 flex justify-between items-center">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.laporan-rekap.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        @elseif(auth()->user()->role === 'owner')
            <a href="{{ route('owner.laporan-rekap.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        @elseif(auth()->user()->role === 'keuangan')
            <a href="{{ route('keuangan.laporan-rekap.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        @endif
        
        <div class="flex gap-2">
            <!-- Export buttons could be added here if needed -->
        </div>
    </div>
</div>
@endsection