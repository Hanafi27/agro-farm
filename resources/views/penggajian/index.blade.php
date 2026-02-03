@extends('layouts.app')

@section('title', 'Penggajian - Ciwidey Agro Farm')

@section('page-title', 'Penggajian')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Penggajian</h2>
            <p class="text-gray-600 mt-1">Kelola data penggajian pegawai berdasarkan absensi</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="openGenerateModal()" class="bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-800 transition-all flex items-center">
                <i class="fas fa-magic mr-2"></i>
                Generate Harian
            </button>
            <button onclick="openGenerateRangeModal()" class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-800 transition-all flex items-center">
                <i class="fas fa-calendar-alt mr-2"></i>
                Generate Rentang
            </button>
            <a href="{{ route('admin.penggajian.create') }}" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Penggajian
            </a>
            <button onclick="confirmDeleteAll()" class="bg-gradient-to-r from-red-500 to-red-700 text-white px-6 py-3 rounded-lg hover:from-red-600 hover:to-red-800 transition-all flex items-center">
                <i class="fas fa-trash mr-2"></i>
                Hapus Semua
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Penggajian</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $penggajians->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Gaji</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($penggajians->sum('total_gaji'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $penggajians->where('bulan', now()->month)->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pegawai Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $penggajians->unique('pegawai_id')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Penggajian</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Potongan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penggajians as $penggajian)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-agro-green to-agro-blue rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-sm">{{ substr($penggajian->pegawai->nama, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $penggajian->pegawai->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ $penggajian->pegawai->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $penggajian->pegawai->divisi }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($penggajian->tipe_periode === 'harian')
                                Harian - {{ optional($penggajian->tanggal)->format('d/m/Y') ?? '-' }}
                            @elseif($penggajian->tipe_periode === 'rentang')
                                Rentang - {{ \Illuminate\Support\Str::of($penggajian->keterangan)->replace('Generated rentang tanggal ', '') }}
                            @else
                                Bulanan - {{ \Carbon\Carbon::createFromDate($penggajian->tahun, $penggajian->bulan, 1)->translatedFormat('F Y') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                            H: {{ $penggajian->total_hadir }} | I: {{ $penggajian->total_izin }} | A: {{ $penggajian->total_alfa }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($penggajian->gaji_per_bulan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($penggajian->potongan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($penggajian->total_gaji, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.penggajian.show', $penggajian->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('admin.penggajian.export-slip', $penggajian->id) }}" class="text-purple-600 hover:text-purple-900" title="Export Slip Gaji">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                
                                <a href="{{ route('admin.penggajian.edit', $penggajian->id) }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('admin.penggajian.destroy', $penggajian->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus penggajian ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">Belum ada data penggajian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penggajians instanceof \Illuminate\Pagination\LengthAwarePaginator && $penggajians->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $penggajians->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Generate Penggajian Modal -->
<div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Generate Penggajian Harian</h3>
                <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.penggajian.generate') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="tanggal_generate" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" id="tanggal_generate" name="tanggal" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent" value="{{ now()->toDateString() }}">
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Informasi Generate Harian</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    • Sistem akan membuat penggajian HARIAN untuk semua pegawai berdasarkan absensi pada tanggal terpilih<br>
                                    • Hadir dibayar 1 hari, izin/alfa dipotong 1 hari (gaji per bulan ÷ 30)<br>
                                    • Penggajian yang sudah ada pada tanggal tersebut tidak akan digandakan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeGenerateModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-2 rounded-lg hover:from-purple-600 hover:to-purple-800 transition-all">
                        <i class="fas fa-magic mr-2"></i>
                        Generate Harian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Generate Penggajian Rentang Modal -->
<div id="generateRangeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Generate Penggajian Rentang Tanggal</h3>
                <button onclick="closeGenerateRangeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.penggajian.generate-range') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent" value="{{ now()->subDays(7)->toDateString() }}">
                    </div>
                    
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent" value="{{ now()->toDateString() }}">
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Informasi Generate Rentang</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    • Sistem akan membuat penggajian HARIAN untuk semua pegawai berdasarkan absensi dalam rentang tanggal yang dipilih<br>
                                    • Hadir dibayar 1 hari, izin/alfa dipotong 1 hari (gaji per bulan ÷ 30)<br>
                                    • Penggajian yang sudah ada pada tanggal tersebut tidak akan digandakan<br>
                                    • Proses ini akan memakan waktu lebih lama untuk rentang tanggal yang panjang
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeGenerateRangeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-6 py-2 rounded-lg hover:from-blue-600 hover:to-blue-800 transition-all">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Generate Rentang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function formatCurrencyModal(input) {
    let value = input.value.replace(/\D/g, '');
    let formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    input.value = formatted;
    document.getElementById('gaji_per_bulan_modal').value = value;
}

function openGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
}

function openGenerateRangeModal() {
    document.getElementById('generateRangeModal').classList.remove('hidden');
}

function closeGenerateRangeModal() {
    document.getElementById('generateRangeModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('generateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerateModal();
    }
});

document.getElementById('generateRangeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerateRangeModal();
    }
});

function confirmDeleteAll() {
    if (confirm('Apakah Anda yakin ingin menghapus semua data penggajian? Ini akan menghapus semua data penggajian yang ada.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('admin.penggajian.delete-all.post') }}";
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection 