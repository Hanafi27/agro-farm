@extends('layouts.app')

@section('title', 'Detail Pengajuan Dana - Ciwidey Agro Farm')

@section('page-title', 'Detail Pengajuan Dana')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Pengajuan Dana</h2>
                <p class="text-gray-600 mt-1">Informasi lengkap pengajuan dana bulanan</p>
            </div>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.pengajuan-dana.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            @elseif(auth()->user()->role === 'owner')
            <a href="{{ route('owner.pengajuan-dana.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            @elseif(auth()->user()->role === 'keuangan')
            <a href="{{ route('keuangan.pengajuan-dana.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            @endif
        </div>
    </div>

    <!-- Basic Information -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Informasi Pengajuan</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                            <p class="text-sm text-gray-900">{{ $pengajuanDana->tanggal ? $pengajuanDana->tanggal->format('d F Y') : '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pengajuanDana->divisi == 'peternakan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $pengajuanDana->getDivisiLabel() }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <p class="text-sm text-gray-900">{{ \Carbon\Carbon::createFromDate($pengajuanDana->tahun, $pengajuanDana->bulan, 1)->format('F Y') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pengajuanDana->getStatusBadgeClass() }}">
                        {{ $pengajuanDana->getStatusLabel() }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pemohon</label>
                    <p class="text-sm text-gray-900">{{ $pengajuanDana->submittedBy->name ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Dana</label>
                    <p class="text-sm font-medium text-green-600">Rp {{ number_format($pengajuanDana->getTotalAmount(), 0, ',', '.') }}</p>
                </div>
            </div>
            
            @if($pengajuanDana->keterangan)
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <p class="text-sm text-gray-900">{{ $pengajuanDana->keterangan }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Items List -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Kebutuhan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kebutuhan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kebutuhan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pengajuanDana->items as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($item->jenis_kebutuhan == 'operasional') bg-blue-100 text-blue-800
                                @elseif($item->jenis_kebutuhan == 'gaji') bg-green-100 text-green-800
                                @elseif($item->jenis_kebutuhan == 'konsumsi') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $item->getJenisKebutuhanLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->nama_kebutuhan }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format(round($item->jumlah), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->satuan }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->getFormattedHargaSatuan() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            {{ $item->getFormattedTotalAmount() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->keterangan ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada item kebutuhan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approval Information -->
    @if($pengajuanDana->approvedBy || $pengajuanDana->rejectedBy || $pengajuanDana->realizedBy)
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Informasi Approval</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @if($pengajuanDana->approvedBy)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Disetujui Oleh</label>
                    <p class="text-sm text-gray-900">{{ $pengajuanDana->approvedBy->name }}</p>
                    <p class="text-xs text-gray-500">{{ $pengajuanDana->tanggal_approval ? $pengajuanDana->tanggal_approval->format('d F Y H:i') : '-' }}</p>
                </div>
                @endif
                
                @if($pengajuanDana->rejectedBy)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ditolak Oleh</label>
                    <p class="text-sm text-gray-900">{{ $pengajuanDana->rejectedBy->name }}</p>
                    <p class="text-xs text-gray-500">{{ $pengajuanDana->tanggal_approval ? $pengajuanDana->tanggal_approval->format('d F Y H:i') : '-' }}</p>
                </div>
                @endif
                
                @if($pengajuanDana->realizedBy)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Direalisasikan Oleh</label>
                    <p class="text-sm text-gray-900">{{ $pengajuanDana->realizedBy->name }}</p>
                    <p class="text-xs text-gray-500">{{ $pengajuanDana->tanggal_realisasi ? $pengajuanDana->tanggal_realisasi->format('d F Y H:i') : '-' }}</p>
                </div>
                @endif
                
                @if($pengajuanDana->alasan_rejection)
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <p class="text-sm text-gray-900">{{ $pengajuanDana->alasan_rejection }}</p>
                </div>
                @endif
                
                @if($pengajuanDana->nominal_diberikan)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nominal yang Diberikan</label>
                    <p class="text-sm font-medium text-green-600">Rp {{ number_format($pengajuanDana->nominal_diberikan, 0, ',', '.') }}</p>
                </div>
                @endif
                
                @if($pengajuanDana->bukti_transfer)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Transfer</label>
                    <div class="flex items-center space-x-2">
                        <a href="{{ request()->getSchemeAndHttpHost() }}/storage/{{ $pengajuanDana->bukti_transfer }}" target="_blank" class="text-blue-600 hover:text-blue-900 flex items-center">
                            <i class="fas fa-file-image mr-1"></i>
                            Lihat Bukti Transfer
                        </a>
                        <img src="{{ request()->getSchemeAndHttpHost() }}/storage/{{ $pengajuanDana->bukti_transfer }}" alt="Bukti Transfer" class="w-16 h-16 object-cover rounded border" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500" style="display: none;">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4">
        @if($pengajuanDana->isDraft() && auth()->user()->role === 'admin')
        <a href="{{ route('admin.pengajuan-dana.edit', $pengajuanDana->id) }}" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors flex items-center">
            <i class="fas fa-edit mr-2"></i>
            Edit Pengajuan
        </a>
        @endif
    </div>
</div>

@endsection 