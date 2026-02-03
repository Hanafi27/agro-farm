@extends('layouts.app')

@section('title', 'Riwayat Pengajuan Dana - Ciwidey Agro Farm')

@section('page-title', 'Riwayat Pengajuan Dana')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Pengajuan Dana</h2>
            <p class="text-gray-600 mt-1">Histori pengajuan dana yang telah disetujui, ditolak, atau dana telah diberikan</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="selectAll()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                <i class="fas fa-check-square mr-2"></i>
                Pilih Semua
            </button>
            <button onclick="bulkDelete()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors flex items-center">
                <i class="fas fa-trash mr-2"></i>
                Hapus Terpilih
            </button>
            <a href="{{ route('owner.pengajuan-dana.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Pengajuan
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="realized">Dana Diberikan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                <select id="divisiFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                    <option value="">Semua Divisi</option>
                    <option value="peternakan">Peternakan</option>
                    <option value="perkebunan">Perkebunan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <select id="periodeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                    <option value="">Semua Periode</option>
                    <option value="1">Week 1</option>
                    <option value="2">Week 2</option>
                    <option value="3">Week 3</option>
                    <option value="4">Week 4</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" id="searchFilter" placeholder="Cari pengajuan..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Riwayat</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalCount">{{ $pengajuanDanas->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Disetujui</p>
                    <p class="text-2xl font-bold text-gray-900" id="approvedCount">{{ $pengajuanDanas->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ditolak</p>
                    <p class="text-2xl font-bold text-gray-900" id="rejectedCount">{{ $pengajuanDanas->where('status', 'rejected')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Riwayat Pengajuan Dana</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-agro-green focus:ring-agro-green">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Dana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                    @forelse($pengajuanDanas as $pengajuan)
                    <tr class="hover:bg-gray-50" data-status="{{ $pengajuan->status }}" data-divisi="{{ $pengajuan->divisi }}" data-bulan="{{ $pengajuan->bulan }}" data-tahun="{{ $pengajuan->tahun }}" data-search="{{ strtolower($pengajuan->submittedBy->name ?? '') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="item-checkbox rounded border-gray-300 text-agro-green focus:ring-agro-green" value="{{ $pengajuan->id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pengajuan->tanggal ? $pengajuan->tanggal->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pengajuan->divisi == 'peternakan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $pengajuan->getDivisiLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::createFromDate($pengajuan->tahun, $pengajuan->bulan, 1)->format('F Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            Rp {{ number_format($pengajuan->getTotalAmount(), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pengajuan->getStatusBadgeClass() }}">
                                {{ $pengajuan->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pengajuan->submittedBy->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('owner.pengajuan-dana.show', $pengajuan->id) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="deleteItem({{ $pengajuan->id }})" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium text-gray-400">Belum ada riwayat pengajuan dana</p>
                                <p class="text-sm text-gray-400 mt-1">Riwayat akan muncul setelah pengajuan diproses</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuanDanas instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengajuanDanas->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pengajuanDanas->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mb-4"></div>
        <p class="text-gray-700 font-medium">Memproses...</p>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2" id="successTitle">Berhasil!</h3>
            <p class="text-sm text-gray-500 mb-4" id="successMessage">Operasi berhasil dilakukan.</p>
            <button onclick="closeSuccessModal()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2" id="errorTitle">Error!</h3>
            <p class="text-sm text-gray-500 mb-4" id="errorMessage">Terjadi kesalahan.</p>
            <button onclick="closeErrorModal()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
// Real-time filtering
function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value;
    const divisiFilter = document.getElementById('divisiFilter').value;
    const periodeFilter = document.getElementById('periodeFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('#tableBody tr');
    let visibleCount = 0;
    let approvedCount = 0;
    let rejectedCount = 0;
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const divisi = row.getAttribute('data-divisi');
        const bulan = row.getAttribute('data-bulan');
        const tahun = row.getAttribute('data-tahun');
        const search = row.getAttribute('data-search');
        
        let show = true;
        
        if (statusFilter && status !== statusFilter) show = false;
        if (divisiFilter && divisi !== divisiFilter) show = false;
        if (periodeFilter) {
            const [filterBulan, filterTahun] = periodeFilter.split('-');
            if (bulan !== filterBulan || tahun !== filterTahun) show = false;
        }
        if (searchFilter && !search.includes(searchFilter)) show = false;
        
        if (show) {
            row.style.display = '';
            visibleCount++;
            if (status === 'approved') approvedCount++;
            if (status === 'rejected') rejectedCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update statistics
    document.getElementById('totalCount').textContent = visibleCount;
    document.getElementById('approvedCount').textContent = approvedCount;
    document.getElementById('rejectedCount').textContent = rejectedCount;
}

// Event listeners for filters
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('divisiFilter').addEventListener('change', filterTable);
document.getElementById('periodeFilter').addEventListener('change', filterTable);
document.getElementById('searchFilter').addEventListener('input', filterTable);

// Select all functionality
function selectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

document.getElementById('selectAll').addEventListener('change', selectAll);

// Delete single item
function deleteItem(id) {
    if (confirm('Yakin ingin menghapus riwayat pengajuan ini?')) {
        showLoading();
        
        fetch(`{{ route('owner.pengajuan-dana.delete-history', ':id') }}`.replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        }).then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showSuccess('Berhasil!', data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showError('Error!', data.message);
            }
        }).catch(error => {
            hideLoading();
            showError('Error!', 'Terjadi kesalahan: ' + error.message);
        });
    }
}

// Bulk delete
function bulkDelete() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        showError('Error!', 'Pilih item yang akan dihapus terlebih dahulu');
        return;
    }
    
    if (confirm(`Yakin ingin menghapus ${selectedIds.length} riwayat pengajuan yang dipilih?`)) {
        showLoading();
        
        fetch('{{ route("owner.pengajuan-dana.bulk-delete-history") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ids: selectedIds })
        }).then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showSuccess('Berhasil!', data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showError('Error!', data.message);
            }
        }).catch(error => {
            hideLoading();
            showError('Error!', 'Terjadi kesalahan: ' + error.message);
        });
    }
}

// Modal functions
function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
}

function showSuccess(title, message) {
    document.getElementById('successTitle').textContent = title;
    document.getElementById('successMessage').textContent = message;
    document.getElementById('successModal').classList.remove('hidden');
}

function showError(title, message) {
    document.getElementById('errorTitle').textContent = title;
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorModal').classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}
</script>
@endsection 