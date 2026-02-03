@extends('layouts.app')

@section('title', 'Edit Pendapatan - Ciwidey Agro Farm')

@section('page-title', 'Edit Pendapatan')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Edit Pendapatan</h2>
                <p class="text-gray-600 mt-1">Perbarui data pendapatan dari perkebunan atau peternakan</p>
            </div>
            <a href="{{ route('admin.pendapatan-susu.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Data Pendapatan</h3>
        </div>
        
        <form action="{{ route('admin.pendapatan-susu.update', $pendapatanSusu->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $pendapatanSusu->tanggal->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2"></i>Kategori
                    </label>
                    <select name="kategori" id="kategori" required onchange="updateJenisProduk()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="">Pilih Kategori</option>
                        <option value="perkebunan" {{ old('kategori', $pendapatanSusu->kategori) == 'perkebunan' ? 'selected' : '' }}>Perkebunan</option>
                        <option value="peternakan" {{ old('kategori', $pendapatanSusu->kategori) == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                    </select>
                    @error('kategori')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jenis_produk" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-leaf mr-2"></i>Jenis Produk
                    </label>
                    <select name="jenis_produk" id="jenis_produk" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="">Pilih Jenis Produk</option>
                    </select>
                    @error('jenis_produk')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jumlah_liter" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-balance-scale mr-2"></i>Jumlah Produk
                    </label>
                    <input type="number" min="0" name="jumlah_liter" id="jumlah_liter" value="{{ old('jumlah_liter', $pendapatanSusu->jumlah_liter) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors" oninput="hitungTotalPendapatan()">
                    @error('jumlah_liter')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flask mr-2"></i>Satuan
                    </label>
                    <select name="satuan" id="satuan" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors" onchange="hitungTotalPendapatan()">
                        <option value="liter" {{ old('satuan', $pendapatanSusu->satuan) == 'liter' ? 'selected' : '' }}>Liter</option>
                        <option value="kg" {{ old('satuan', $pendapatanSusu->satuan) == 'kg' ? 'selected' : '' }}>Kg</option>
                    </select>
                    @error('satuan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga_per_liter" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave mr-2"></i>Harga per Satuan
                    </label>
                    <input type="number" min="0" name="harga_per_liter" id="harga_per_liter" value="{{ old('harga_per_liter', $pendapatanSusu->harga_per_liter) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors" oninput="hitungTotalPendapatan()">
                    @error('harga_per_liter')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_pendapatan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-coins mr-2"></i>Total Pendapatan
                    </label>
                    <input type="number" min="0" name="total_pendapatan" id="total_pendapatan" value="{{ old('total_pendapatan', $pendapatanSusu->total_pendapatan) }}" readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                </div>
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                    placeholder="Masukkan keterangan (opsional)">{{ old('keterangan', $pendapatanSusu->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Informasi Kategori</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            • <strong>Perkebunan:</strong> Produk Teh<br>
                            • <strong>Peternakan:</strong> Susu Kambing atau Susu Sapi<br>
                            • Data pendapatan akan dicatat berdasarkan kategori dan jenis produk
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.pendapatan-susu.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Update Pendapatan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateJenisProduk() {
    const kategori = document.getElementById('kategori').value;
    const jenisProdukSelect = document.getElementById('jenis_produk');
    const currentJenisProduk = '{{ old("jenis_produk", $pendapatanSusu->jenis_produk) }}';
    
    // Clear existing options
    jenisProdukSelect.innerHTML = '<option value="">Pilih Jenis Produk</option>';
    
    if (kategori === 'perkebunan') {
        const option = document.createElement('option');
        option.value = 'teh';
        option.textContent = 'Teh';
        if (currentJenisProduk === 'teh') {
            option.selected = true;
        }
        jenisProdukSelect.appendChild(option);
    } else if (kategori === 'peternakan') {
        const option1 = document.createElement('option');
        option1.value = 'susu_kambing';
        option1.textContent = 'Susu Kambing';
        if (currentJenisProduk === 'susu_kambing') {
            option1.selected = true;
        }
        jenisProdukSelect.appendChild(option1);
        
        const option2 = document.createElement('option');
        option2.value = 'susu_sapi';
        option2.textContent = 'Susu Sapi';
        if (currentJenisProduk === 'susu_sapi') {
            option2.selected = true;
        }
        jenisProdukSelect.appendChild(option2);
    }
}

function hitungTotalPendapatan() {
    const jumlah = parseInt(document.getElementById('jumlah_liter').value) || 0;
    const harga = parseInt(document.getElementById('harga_per_liter').value) || 0;
    document.getElementById('total_pendapatan').value = Math.round(jumlah * harga);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateJenisProduk();
    hitungTotalPendapatan();
});
</script>
@endsection
