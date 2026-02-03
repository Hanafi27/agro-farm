@extends('layouts.app')

@section('title', 'Laporan Rekap - Ciwidey Agro Farm')

@section('page-title', 'Laporan Rekap')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Rekap Bulanan</h3>
                <p class="text-sm text-gray-500 mt-1">Pilih periode dan divisi untuk mengunduh rekap</p>
            </div>
            <div class="text-xs inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>
                Lampiran (gambar) ikut di PDF
            </div>
        </div>
        @php
            $routeName = request()->route()->getName();
            $parts = explode('.', $routeName);
            $prefix = $parts[0] ?? 'admin';
            $pdfRoute = $prefix.'.laporan-realisasi.export-rekapan-bulanan-pdf';
            $excelRoute = $prefix.'.laporan-realisasi.export-rekapan-bulanan-excel';
        @endphp
        <form class="space-y-6" method="POST" action="{{ route($excelRoute) }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select name="bulan" id="filter-bulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent" onchange="updateFilter()">
                        @foreach($months as $num=>$label)
                            <option value="{{ $num }}" {{ $num == $filterBulan ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select name="tahun" id="filter-tahun" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent" onchange="updateFilter()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $y == $filterTahun ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                    <select name="divisi" id="filter-divisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent" onchange="updateFilter()">
                        <option value="all" {{ $filterDivisi == 'all' ? 'selected' : '' }}>Semua Divisi</option>
                        <option value="perkebunan" {{ $filterDivisi == 'perkebunan' ? 'selected' : '' }}>Perkebunan</option>
                        <option value="peternakan" {{ $filterDivisi == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button formaction="{{ route($pdfRoute) }}" class="bg-red-500 text-white px-5 py-2.5 rounded hover:bg-red-600 inline-flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i> Export PDF
                </button>
                <button class="bg-green-600 text-white px-5 py-2.5 rounded hover:bg-green-700 inline-flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </button>
            </div>
        </form>
    </div>

    <!-- Data Rekapan yang Sudah Ada -->
    @if($allRekapan->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Rekapan yang Tersedia</h3>
        
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debit (Pencairan)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kredit (Pengeluaran)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($allRekapan as $rekapan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $rekapan->getPeriodeLabel() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $rekapan->divisi === 'combined' ? 'bg-purple-100 text-purple-800' : 
                                   ($rekapan->divisi === 'peternakan' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $rekapan->getDivisiLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($rekapan->getDebitAmount() ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($rekapan->getKreditAmount() ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="text-gray-700">
                                Rp {{ number_format(abs($rekapan->getSaldoAmount() ?? 0), 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $rekapan->generatedBy->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.laporan-rekap.show', $rekapan->id) }}" class="text-agro-green hover:text-agro-blue">Detail</a>
                            @elseif(auth()->user()->role === 'owner')
                                <a href="{{ route('owner.laporan-rekap.show', $rekapan->id) }}" class="text-agro-green hover:text-agro-blue">Detail</a>
                            @elseif(auth()->user()->role === 'keuangan')
                                <a href="{{ route('keuangan.laporan-rekap.show', $rekapan->id) }}" class="text-agro-green hover:text-agro-blue">Detail</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($allRekapan->hasPages())
        <div class="mt-4">
            {{ $allRekapan->links() }}
        </div>
        @endif
    </div>
    @else
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <div class="text-center py-8">
            <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Rekapan</h3>
            <p class="text-gray-500">Data rekapan akan muncul setelah ada laporan realisasi yang di-approve.</p>
        </div>
    </div>
    @endif

    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Panduan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div class="bg-blue-50 border border-blue-100 rounded p-3">
                <div class="font-medium text-blue-800 mb-1"><i class="fas fa-calendar mr-1"></i> Periode</div>
                <p>Pilih bulan dan tahun yang ingin direkap.</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded p-3">
                <div class="font-medium text-green-800 mb-1"><i class="fas fa-layer-group mr-1"></i> Divisi</div>
                <p>Pilih gabungan untuk semua divisi, atau pilih divisi tertentu.</p>
            </div>
            <div class="bg-purple-50 border border-purple-100 rounded p-3">
                <div class="font-medium text-purple-800 mb-1"><i class="fas fa-paperclip mr-1"></i> Lampiran</div>
                <p>PDF menyertakan lampiran bukti (format gambar) di bagian akhir.</p>
            </div>
        </div>
    </div>
</div>

<script>
function updateFilter() {
    const bulan = document.getElementById('filter-bulan').value;
    const tahun = document.getElementById('filter-tahun').value;
    const divisi = document.getElementById('filter-divisi').value;
    
    // Build URL with current parameters
    const url = new URL(window.location);
    url.searchParams.set('bulan', bulan);
    url.searchParams.set('tahun', tahun);
    url.searchParams.set('divisi', divisi);
    
    // Reload page with new parameters
    window.location.href = url.toString();
}
</script>

@endsection


