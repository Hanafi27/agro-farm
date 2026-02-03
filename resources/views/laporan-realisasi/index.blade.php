@extends('layouts.app')

@section('title', 'Laporan Realisasi - Ciwidey Agro Farm')

@section('page-title', 'Laporan Realisasi')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Laporan Realisasi</h2>
                <p class="text-gray-600 mt-1">Kelola laporan realisasi penggunaan dana bulanan</p>
            </div>
            <div class="flex space-x-3">
                @if(auth()->user()->role === 'admin')
                <button onclick="confirmDeleteAll()" class="bg-gradient-to-r from-red-500 to-red-700 text-white px-6 py-3 rounded-lg hover:from-red-600 hover:to-red-800 transition-all flex items-center">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Semua Laporan
                </button>
                <a href="{{ route('admin.laporan-realisasi.create') }}" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Laporan
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Laporan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $laporanRealisasis->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($laporanRealisasis->sum('total_pendapatan'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Biaya</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($laporanRealisasis->sum('total_biaya'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Table -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Laporan Realisasi</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Divisi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Periode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Pendapatan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Biaya
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dibuat Oleh
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($laporanRealisasis as $index => $laporan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $index + 1 + ($laporanRealisasis->currentPage() - 1) * $laporanRealisasis->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $laporan->getDivisiLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $laporan->getBulanLabel() }} {{ $laporan->tahun }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($laporan->total_pendapatan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($laporan->total_biaya, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $laporan->submittedBy->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $laporan->tanggal ? $laporan->tanggal->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @php
                                    $role = auth()->user()->role;
                                    $showRoute = $role === 'owner' ? route('owner.laporan-realisasi.show', $laporan->id) : ($role === 'keuangan' ? route('keuangan.laporan-realisasi.show', $laporan->id) : route('admin.laporan-realisasi.show', $laporan->id));
                                @endphp
                                <a href="{{ $showRoute }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.laporan-realisasi.edit', $laporan->id) }}" 
                                   class="text-green-600 hover:text-green-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button onclick="deleteLaporan({{ $laporan->id }})" 
                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                                
                                @php
                                    $pdfRoute = $role === 'owner' ? route('owner.laporan-realisasi.export-pdf', $laporan->id) : ($role === 'keuangan' ? route('keuangan.laporan-realisasi.export-pdf', $laporan->id) : route('admin.laporan-realisasi.export-pdf', $laporan->id));
                                    $excelRoute = $role === 'owner' ? route('owner.laporan-realisasi.export-excel', $laporan->id) : ($role === 'keuangan' ? route('keuangan.laporan-realisasi.export-excel', $laporan->id) : route('admin.laporan-realisasi.export-excel', $laporan->id));
                                @endphp
                                <a href="{{ $pdfRoute }}" class="text-red-600 hover:text-red-900" title="Export PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a href="{{ $excelRoute }}" class="text-green-600 hover:text-green-900" title="Export Excel">
                                    <i class="fas fa-file-excel"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-alt text-4xl text-gray-300 mb-2"></i>
                                <p class="text-lg font-medium text-gray-400">Belum ada laporan realisasi</p>
                                <p class="text-sm text-gray-400">Mulai dengan membuat laporan realisasi baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($laporanRealisasis->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $laporanRealisasis->links() }}
        </div>
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

<!-- Removed Export Rekapan Tahunan Modal -->

<!-- Delete Laporan Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Hapus Laporan</h3>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">
                Apakah Anda yakin ingin menghapus laporan realisasi ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                        Hapus
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

function deleteLaporan(id) {
    document.getElementById('deleteForm').action = "{{ route('admin.laporan-realisasi.destroy', ':id') }}".replace(':id', id);
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function confirmDeleteAll() {
    if (!confirm('Hapus semua laporan realisasi? Tindakan ini tidak bisa dibatalkan.')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('admin.laporan-realisasi.delete-all') }}";
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});
</script>
@endsection 