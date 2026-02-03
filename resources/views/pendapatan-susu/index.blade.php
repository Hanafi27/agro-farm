@extends('layouts.app')

@section('title', 'Pendapatan - Ciwidey Agro Farm')

@section('page-title', 'Pendapatan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Pendapatan</h2>
            <p class="text-gray-600 mt-1">Kelola pendapatan dari perkebunan dan peternakan</p>
        </div>
        <a href="{{ route('admin.pendapatan-susu.create') }}" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Pendapatan
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="GET" action="{{ route('admin.pendapatan-susu.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        placeholder="Cari kategori, jenis produk, atau keterangan..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                </div>
                
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="kategori" id="kategori" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                {{ $kategori == 'perkebunan' ? 'Perkebunan' : 'Peternakan' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="jenis_produk" class="block text-sm font-medium text-gray-700 mb-2">Jenis Produk</label>
                    <select name="jenis_produk" id="jenis_produk" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisProduks as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis_produk') == $jenis ? 'selected' : '' }}>
                                @switch($jenis)
                                    @case('teh')
                                        Teh
                                        @break
                                    @case('susu_kambing')
                                        Susu Kambing
                                        @break
                                    @case('susu_sapi')
                                        Susu Sapi
                                        @break
                                    @default
                                        {{ ucfirst($jenis) }}
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="tanggal_awal" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ request('tanggal_awal') }}" 
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}" 
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Cari
                    </button>
                    <a href="{{ route('admin.pendapatan-susu.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-leaf text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Produksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProduksi, 0, ',', '.') }} Unit</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($hariIni, 0, ',', '.') }} Unit</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-coins text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata/Hari</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($rataRataHari, 1, ',', '.') }} Unit</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Daftar Pendapatan</h3>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-agro-green focus:ring-agro-green" onchange="toggleSelectAll()">
                        <label for="selectAll" class="ml-2 text-sm text-gray-600">Pilih Semua</label>
                    </div>
                    <button type="button" id="bulkDeleteBtn" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" onclick="bulkDeletePendapatan()" disabled>
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Terpilih
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-agro-green focus:ring-agro-green" onchange="toggleSelectAll()">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga/Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pendapatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendapatanSusus as $pendapatan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_pendapatan[]" value="{{ $pendapatan->id }}" class="pendapatan-checkbox rounded border-gray-300 text-agro-green focus:ring-agro-green" onchange="updateBulkDeleteButton()">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pendapatan->tanggal ? $pendapatan->tanggal->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pendapatan->getKategoriBadgeClass() }}">
                                {{ $pendapatan->getKategoriLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pendapatan->getJenisProdukBadgeClass() }}">
                                {{ $pendapatan->getJenisProdukLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            {{ number_format($pendapatan->jumlah_liter, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                            {{ ucfirst($pendapatan->satuan ?? 'liter') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            Rp {{ number_format($pendapatan->harga_per_liter, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            Rp {{ number_format($pendapatan->total_pendapatan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $pendapatan->keterangan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.pendapatan-susu.show', $pendapatan->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.pendapatan-susu.edit', $pendapatan->id) }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="text-red-600 hover:text-red-900" onclick="deletePendapatan({{ $pendapatan->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <i class="fas fa-leaf text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium text-gray-400">Belum ada data pendapatan</p>
                                <p class="text-sm text-gray-400 mt-1">Silakan tambah pendapatan baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendapatanSusus instanceof \Illuminate\Pagination\LengthAwarePaginator && $pendapatanSusus->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pendapatanSusus->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <i class="fas fa-spinner fa-spin text-blue-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Memproses...</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Sedang menghapus data pendapatan...</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4" id="successTitle">Berhasil!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="successMessage">Data berhasil dihapus</p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="hideSuccess()" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-times text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4" id="errorTitle">Error!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="errorMessage">Terjadi kesalahan</p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="hideError()" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showLoading() {
    document.getElementById('loadingModal').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingModal').classList.add('hidden');
}

function showSuccess(title, message) {
    document.getElementById('successTitle').textContent = title;
    document.getElementById('successMessage').textContent = message;
    document.getElementById('successModal').classList.remove('hidden');
}

function hideSuccess() {
    document.getElementById('successModal').classList.add('hidden');
}

function showError(title, message) {
    document.getElementById('errorTitle').textContent = title;
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorModal').classList.remove('hidden');
}

function hideError() {
    document.getElementById('errorModal').classList.add('hidden');
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');
    const pendapatanCheckboxes = document.querySelectorAll('.pendapatan-checkbox');
    
    const isChecked = selectAllCheckbox.checked || selectAllHeaderCheckbox.checked;
    
    pendapatanCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    
    // Sync both select all checkboxes
    selectAllCheckbox.checked = isChecked;
    selectAllHeaderCheckbox.checked = isChecked;
    
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const selectedCheckboxes = document.querySelectorAll('.pendapatan-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');
    
    if (selectedCheckboxes.length > 0) {
        bulkDeleteBtn.disabled = false;
        bulkDeleteBtn.textContent = `Hapus Terpilih (${selectedCheckboxes.length})`;
    } else {
        bulkDeleteBtn.disabled = true;
        bulkDeleteBtn.innerHTML = '<i class="fas fa-trash mr-2"></i>Hapus Terpilih';
    }
    
    // Update select all checkboxes
    const totalCheckboxes = document.querySelectorAll('.pendapatan-checkbox').length;
    if (selectedCheckboxes.length === totalCheckboxes && totalCheckboxes > 0) {
        selectAllCheckbox.checked = true;
        selectAllHeaderCheckbox.checked = true;
    } else {
        selectAllCheckbox.checked = false;
        selectAllHeaderCheckbox.checked = false;
    }
}

function deletePendapatan(id) {
    if (confirm('Yakin ingin menghapus pendapatan ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
        showLoading();
        
        console.log('Deleting pendapatan with ID:', id);
        
        fetch(`/admin/pendapatan-susu/${id}`, {
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
                // Reload page after successful deletion
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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

function bulkDeletePendapatan() {
    const selectedCheckboxes = document.querySelectorAll('.pendapatan-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
    
    if (selectedIds.length === 0) {
        showError('Error!', 'Pilih minimal satu pendapatan untuk dihapus');
        return;
    }
    
    if (confirm(`Yakin ingin menghapus ${selectedIds.length} pendapatan yang dipilih?\n\nTindakan ini tidak dapat dibatalkan.`)) {
        showLoading();
        
        console.log('Bulk deleting pendapatan with IDs:', selectedIds);
        
        fetch('/admin/pendapatan-susu/bulk-delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                ids: selectedIds
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
                // Reload page after successful deletion
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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
</script>
@endsection 