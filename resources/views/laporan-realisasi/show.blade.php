@extends('layouts.app')

@section('title', 'Detail Laporan Realisasi - Ciwidey Agro Farm')

@section('page-title', 'Detail Laporan Realisasi')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Laporan Realisasi</h2>
                <p class="text-gray-600 mt-1">Informasi lengkap laporan realisasi penggunaan dana</p>
            </div>
            <div class="flex space-x-3">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.laporan-realisasi.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    
                    @if(true)
                    <a href="{{ route('admin.laporan-realisasi.export-pdf', $laporanRealisasi->id) }}" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Export PDF
                    </a>
                    
                    <a href="{{ route('admin.laporan-realisasi.export-excel', $laporanRealisasi->id) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    
                    <a href="{{ route('admin.laporan-realisasi.export-rekapan-pdf', $laporanRealisasi->id) }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Export Rekapan PDF
                    </a>
                    
                    <a href="{{ route('admin.laporan-realisasi.export-rekapan-excel', $laporanRealisasi->id) }}" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition-colors flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Rekapan Excel
                    </a>
                    @endif
                @elseif(auth()->user()->role === 'owner')
                    <a href="{{ route('owner.laporan-realisasi.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    
                    @if(true)
                    <a href="{{ route('owner.laporan-realisasi.export-pdf', $laporanRealisasi->id) }}" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Export PDF
                    </a>
                    
                    <a href="{{ route('owner.laporan-realisasi.export-excel', $laporanRealisasi->id) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    
                    <a href="{{ route('owner.laporan-realisasi.export-rekapan-pdf', $laporanRealisasi->id) }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Export Rekapan PDF
                    </a>
                    
                    <a href="{{ route('owner.laporan-realisasi.export-rekapan-excel', $laporanRealisasi->id) }}" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition-colors flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Rekapan Excel
                    </a>
                    @endif
                @elseif(auth()->user()->role === 'keuangan')
                    <a href="{{ route('keuangan.laporan-realisasi.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    
                    @if(true)
                    <a href="{{ route('keuangan.laporan-realisasi.export-pdf', $laporanRealisasi->id) }}" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Export PDF
                    </a>
                    
                    <a href="{{ route('keuangan.laporan-realisasi.export-excel', $laporanRealisasi->id) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    
                    <a href="{{ route('keuangan.laporan-realisasi.export-rekapan-pdf', $laporanRealisasi->id) }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Export Rekapan PDF
                    </a>
                    
                    <a href="{{ route('keuangan.laporan-realisasi.export-rekapan-excel', $laporanRealisasi->id) }}" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition-colors flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Rekapan Excel
                    </a>
                    @endif
                @endif
            </div>
        </div>
    </div>


    <!-- Basic Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Laporan</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Divisi:</span>
                    <span class="font-medium">{{ $laporanRealisasi->getDivisiLabel() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Periode:</span>
                    <span class="font-medium">{{ $laporanRealisasi->getBulanLabel() }} {{ $laporanRealisasi->tahun }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="font-medium">{{ $laporanRealisasi->tanggal ? $laporanRealisasi->tanggal->format('d F Y') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Dibuat Oleh:</span>
                    <span class="font-medium">{{ $laporanRealisasi->submittedBy->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Keuangan</h3>
            <div class="space-y-3">
                <div class="flex justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-blue-800 font-medium">Total Pendapatan:</span>
                    <span class="text-blue-800 font-bold">Rp {{ number_format($laporanRealisasi->total_pendapatan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-green-800 font-medium">Total Tenaga & Konsumsi:</span>
                    <span class="text-green-800 font-bold">Rp {{ number_format($laporanRealisasi->total_tenaga_konsumsi, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-yellow-800 font-medium">Total Alat & Bahan:</span>
                    <span class="text-yellow-800 font-bold">Rp {{ number_format($laporanRealisasi->total_alat_bahan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between p-3 bg-purple-50 rounded-lg">
                    <span class="text-purple-800 font-medium">Total Biaya:</span>
                    <span class="text-purple-800 font-bold">Rp {{ number_format($laporanRealisasi->total_biaya, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Details -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detail Item Realisasi</h3>
        </div>
        
        <div class="p-6">
            <!-- Pendapatan Section -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-blue-800 mb-4">PENDAPATAN</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Nama Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Keterangan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($laporanRealisasi->items()->where('kategori', 'pendapatan')->get() as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->nama_item }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->getFormattedJumlah() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->satuan }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->getFormattedHargaSatuan() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $item->getFormattedTotalAmount() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @include('laporan-realisasi.partials.attachments-display', ['item' => $item])
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <form method="POST" action="{{ route('admin.laporan-realisasi.destroy-item', [$laporanRealisasi->id, $item->id]) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-sm text-gray-500 text-center">Tidak ada data pendapatan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tenaga Kerja & Konsumsi Section -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-green-800 mb-4">TENAGA KERJA & KONSUMSI</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Nama Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Keterangan</th>
                                @if(auth()->user()->role === 'admin')
                                <th class="px-4 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($laporanRealisasi->items()->where('kategori', 'tenaga_konsumsi')->get() as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->nama_item }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->getFormattedJumlah() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->satuan }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->getFormattedHargaSatuan() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $item->getFormattedTotalAmount() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @include('laporan-realisasi.partials.attachments-display', ['item' => $item])
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <form method="POST" action="{{ route('admin.laporan-realisasi.destroy-item', [$laporanRealisasi->id, $item->id]) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-sm text-gray-500 text-center">Tidak ada data tenaga kerja & konsumsi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Alat & Bahan Section -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-yellow-800 mb-4">ALAT & BAHAN</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-yellow-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Nama Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Keterangan</th>
                                @if(auth()->user()->role === 'admin')
                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($laporanRealisasi->items()->where('kategori', 'alat_bahan')->get() as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->nama_item }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->getFormattedJumlah() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->satuan }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->getFormattedHargaSatuan() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $item->getFormattedTotalAmount() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @include('laporan-realisasi.partials.attachments-display', ['item' => $item])
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <form method="POST" action="{{ route('admin.laporan-realisasi.destroy-item', [$laporanRealisasi->id, $item->id]) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-sm text-gray-500 text-center">Tidak ada data alat & bahan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Keterangan -->
    @if($laporanRealisasi->keterangan)
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Keterangan</h3>
        <p class="text-gray-700">{{ $laporanRealisasi->keterangan }}</p>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-4">
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.laporan-realisasi.edit', $laporanRealisasi->id) }}" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors flex items-center">
            <i class="fas fa-edit mr-2"></i>
            Edit Laporan
        </a>
        
        <button onclick="sendLaporan({{ $laporanRealisasi->id }})" class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition-colors flex items-center">
            <i class="fas fa-paper-plane mr-2"></i>
            Kirim untuk Approval
        </button>
        @endif
        
        @if(auth()->user()->role === 'owner')
        <button onclick="approveLaporan({{ $laporanRealisasi->id }})" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors flex items-center">
            <i class="fas fa-check mr-2"></i>
            Setujui Laporan
        </button>
        @endif
    </div>
</div>

<!-- Send Laporan Modal -->
<div id="sendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-paper-plane text-orange-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Kirim Laporan</h3>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">
                Apakah Anda yakin ingin mengirim laporan ini untuk approval? Laporan tidak dapat diedit setelah dikirim.
            </p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeSendModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <form id="sendForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                        Kirim Laporan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Approve Laporan Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Setujui Laporan</h3>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">
                Apakah Anda yakin ingin menyetujui laporan realisasi ini?
            </p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeApproveModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <form id="approveForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                        Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function sendLaporan(id) {
    document.getElementById('sendForm').action = "{{ route('admin.laporan-realisasi.send', ':id') }}".replace(':id', id);
    document.getElementById('sendModal').classList.remove('hidden');
}

function closeSendModal() {
    document.getElementById('sendModal').classList.add('hidden');
}

function approveLaporan(id) {
    @if(auth()->user()->role === 'owner')
        document.getElementById('approveForm').action = "{{ route('owner.laporan-realisasi.approve', ':id') }}".replace(':id', id);
    @elseif(auth()->user()->role === 'admin')
        document.getElementById('approveForm').action = "{{ route('admin.laporan-realisasi.approve', ':id') }}".replace(':id', id);
    @endif
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});
</script>
@endsection 