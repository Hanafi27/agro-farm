@extends('layouts.app')

@section('title', 'Pengajuan Dana - Ciwidey Agro Farm')

@section('page-title', 'Pengajuan Dana')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Pengajuan Dana</h2>
            <p class="text-gray-600 mt-1">Kelola pengajuan dana bulanan untuk divisi peternakan dan perkebunan</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('owner.pengajuan-dana.history') }}" class="bg-purple-500 text-white px-6 py-3 rounded-lg hover:bg-purple-600 transition-all flex items-center">
                <i class="fas fa-history mr-2"></i>
                Riwayat Pengajuan
            </a>
            <form action="{{ route('owner.pengajuan-dana.approve-all-submit') }}" method="POST" onsubmit="return confirm('Yakin ingin MENYETUJUI SEMUA pengajuan submit?')">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-all flex items-center">
                    <i class="fas fa-check mr-2"></i>
                    Setujui Semua Pengajuan
                </button>
            </form>
            <!-- Tombol Bulk Delete untuk owner -->
            <button onclick="bulkDeletePengajuan()" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-all flex items-center">
                <i class="fas fa-trash mr-2"></i>
                Hapus Terpilih
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pengajuan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pengajuanDanas->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Menunggu Approval</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pengajuanDanas->where('status', 'submit')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Pengajuan Dana</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
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
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pengajuanDanas as $pengajuan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_pengajuan[]" value="{{ $pengajuan->id }}" class="pengajuan-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
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
                                
                                @if($pengajuan->isPending())
                                <button onclick="approvePengajuan({{ $pengajuan->id }})" class="approve-btn bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 transition-colors text-xs font-medium flex items-center" title="Setujui Pengajuan">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </button>
                                <button onclick="rejectPengajuan({{ $pengajuan->id }})" class="reject-btn bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition-colors text-xs font-medium flex items-center" title="Tolak Pengajuan">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                                @endif
                                
                                <!-- Tombol Hapus untuk owner (kecuali yang sudah direalisasikan) -->
                                @if($pengajuan->status !== 'realized')
                                <button onclick="deletePengajuan({{ $pengajuan->id }})" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium text-gray-400">Belum ada data pengajuan dana</p>
                                <p class="text-sm text-gray-400 mt-1">Silakan tunggu pengajuan dari admin</p>
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
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mb-4"></div>
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

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center mb-4">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Tolak Pengajuan</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="alasan_rejection" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea name="alasan_rejection" id="alasan_rejection" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Masukkan alasan penolakan"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
    window.location.reload();
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}

function approvePengajuan(id) {
    if (confirm('Yakin ingin MENYETUJUI pengajuan ini?\n\nSetelah disetujui, pengajuan akan dapat direalisasikan oleh bagian keuangan.')) {
        showLoading();
        
        console.log('Approving pengajuan with ID:', id);
        
        fetch(`/owner/pengajuan-dana/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        }).then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            hideLoading();
            if (data.success) {
                showSuccess('Berhasil!', data.message);
            } else {
                showError('Error!', data.message);
            }
        }).catch(error => {
            console.error('Fetch error:', error);
            hideLoading();
            showError('Error!', 'Terjadi kesalahan: ' + error.message);
        });
    }
}

function deletePengajuan(id) {
    if (confirm('Yakin ingin menghapus pengajuan ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
        showLoading();
        
        console.log('Deleting pengajuan with ID:', id);
        
        fetch(`/owner/pengajuan-dana/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        }).then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            hideLoading();
            if (data.success) {
                showSuccess('Berhasil!', data.message);
            } else {
                showError('Error!', data.message);
            }
        }).catch(error => {
            console.error('Fetch error:', error);
            hideLoading();
            showError('Error!', 'Terjadi kesalahan: ' + error.message);
        });
    }
}

function bulkDeletePengajuan() {
    const checkboxes = document.querySelectorAll('input[name="selected_pengajuan[]"]:checked');
    if (checkboxes.length === 0) {
        alert('Pilih pengajuan yang akan dihapus terlebih dahulu.');
        return;
    }
    
    if (confirm(`Yakin ingin menghapus ${checkboxes.length} pengajuan yang dipilih?\n\nTindakan ini tidak dapat dibatalkan.`)) {
        showLoading();
        
        const ids = Array.from(checkboxes).map(cb => cb.value);
        console.log('Bulk deleting pengajuan with IDs:', ids);
        
        fetch(`/owner/pengajuan-dana/bulk-delete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                ids: ids
            })
        }).then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            hideLoading();
            if (data.success) {
                showSuccess('Berhasil!', data.message);
            } else {
                showError('Error!', data.message);
            }
        }).catch(error => {
            console.error('Fetch error:', error);
            hideLoading();
            showError('Error!', 'Terjadi kesalahan: ' + error.message);
        });
    }
}

function rejectPengajuan(id) {
    document.getElementById('rejectForm').action = `{{ route('owner.pengajuan-dana.reject', ':id') }}`.replace(':id', id);
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('successModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSuccessModal();
    }
});

document.getElementById('errorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeErrorModal();
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Select all checkbox functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.pengajuan-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Update select all checkbox when individual checkboxes change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('pengajuan-checkbox')) {
        const checkboxes = document.querySelectorAll('.pengajuan-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        const checkedCheckboxes = document.querySelectorAll('.pengajuan-checkbox:checked');
        
        if (checkedCheckboxes.length === checkboxes.length) {
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.checked = false;
        }
    }
});
</script>
@endsection 