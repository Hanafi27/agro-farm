@extends('layouts.app')

@section('title', 'Tambah Pengajuan Dana - Ciwidey Agro Farm')

@section('page-title', 'Tambah Pengajuan Dana')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tambah Pengajuan Dana Baru</h2>
                <p class="text-gray-600 mt-1">Input pengajuan dana bulanan untuk divisi peternakan atau perkebunan</p>
            </div>
            <a href="{{ route('admin.pengajuan-dana.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Data Pengajuan Dana</h3>
        </div>
        
        <form action="{{ route('admin.pengajuan-dana.store') }}" method="POST" class="p-6 space-y-6" id="pengajuanForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="divisi" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-2"></i>Divisi
                    </label>
                    <select name="divisi" id="divisi" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="">Pilih Divisi</option>
                        <option value="peternakan" {{ old('divisi') == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                        <option value="perkebunan" {{ old('divisi') == 'perkebunan' ? 'selected' : '' }}>Perkebunan</option>
                    </select>
                    @error('divisi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2"></i>Bulan
                    </label>
                    <select name="bulan" id="bulan" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="">Pilih Bulan</option>
                        <option value="1" {{ old('bulan', $currentMonth) == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ old('bulan', $currentMonth) == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ old('bulan', $currentMonth) == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ old('bulan', $currentMonth) == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ old('bulan', $currentMonth) == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ old('bulan', $currentMonth) == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ old('bulan', $currentMonth) == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ old('bulan', $currentMonth) == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ old('bulan', $currentMonth) == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ old('bulan', $currentMonth) == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ old('bulan', $currentMonth) == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ old('bulan', $currentMonth) == 12 ? 'selected' : '' }}>Desember</option>
                    </select>
                    @error('bulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-year mr-2"></i>Tahun
                </label>
                <input type="number" name="tahun" id="tahun" value="{{ old('tahun', $currentYear) }}" required min="2020" max="2030"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                @error('tahun')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dynamic Items Section -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Daftar Kebutuhan</h4>
                    <button type="button" onclick="addItem()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Baris
                    </button>
                </div>

                <!-- Items Table Header -->
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="grid grid-cols-7 gap-4 text-sm font-medium text-gray-700">
                        <div>No</div>
                        <div>Jenis Kebutuhan</div>
                        <div>Nama Kebutuhan</div>
                        <div>Jumlah</div>
                        <div>Satuan</div>
                        <div>Harga Satuan</div>
                        <div>Total</div>
                    </div>
                </div>

                <div id="items-container" class="space-y-3">
                    <!-- Items will be added here dynamically -->
                </div>

                <!-- Total Summary -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex justify-between items-center">
                        <h5 class="text-lg font-semibold text-blue-800">Total Pengajuan</h5>
                        <div class="text-2xl font-bold text-blue-800" id="grandTotal">Rp 0</div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Informasi Pengajuan</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                • Pengajuan akan disimpan sebagai draft<br>
                                • Setelah selesai, klik "Kirim Pengajuan" untuk approval<br>
                                • Owner akan menyetujui atau menolak pengajuan<br>
                                • Keuangan akan merealisasikan dana yang disetujui
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                    placeholder="Masukkan keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.pengajuan-dana.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = 0;

function addItem() {
    const container = document.getElementById('items-container');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'bg-white p-4 rounded-lg border border-gray-200 shadow-sm';
    itemDiv.innerHTML = `
        <div class="grid grid-cols-7 gap-4 items-center">
            <div class="text-center font-medium text-gray-700">${itemIndex + 1}</div>
            <div>
                <select name="items[${itemIndex}][jenis_kebutuhan]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm">
                    <option value="">Pilih Jenis</option>
                    <option value="operasional">Operasional</option>
                    <option value="gaji">Gaji</option>
                    <option value="konsumsi">Konsumsi</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <input type="text" name="items[${itemIndex}][nama_kebutuhan]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="Nama kebutuhan">
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][jumlah]" required step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="Jumlah" onchange="calculateTotal(this)">
            </div>
            <div>
                <input type="text" name="items[${itemIndex}][satuan]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="Satuan">
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][harga_satuan]" required min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="Harga" onchange="calculateTotal(this)">
            </div>
            <div class="flex items-center space-x-2">
                <input type="text" readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm"
                    placeholder="Total">
                <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 p-1">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </div>
        </div>
        <div class="mt-3">
            <input type="text" name="items[${itemIndex}][keterangan]"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                placeholder="Keterangan (opsional)">
        </div>
    `;
    container.appendChild(itemDiv);
    itemIndex++;
    updateItemNumbers();
}

function removeItem(button) {
    button.closest('.bg-white').remove();
    updateItemNumbers();
    calculateGrandTotal();
}

function updateItemNumbers() {
    const items = document.querySelectorAll('#items-container .bg-white');
    items.forEach((item, index) => {
        item.querySelector('.text-center').textContent = index + 1;
    });
}

function calculateTotal(input) {
    const itemDiv = input.closest('.bg-white');
    const jumlahInput = itemDiv.querySelector('input[name*="[jumlah]"]');
    const hargaInput = itemDiv.querySelector('input[name*="[harga_satuan]"]');
    const totalInput = itemDiv.querySelector('input[readonly]');
    
    const jumlah = parseFloat(jumlahInput.value) || 0;
    const harga = parseFloat(hargaInput.value) || 0;
    const total = jumlah * harga;
    
    totalInput.value = 'Rp ' + total.toLocaleString('id-ID');
    calculateGrandTotal();
}

function calculateGrandTotal() {
    const totalInputs = document.querySelectorAll('#items-container input[readonly]');
    let grandTotal = 0;
    
    totalInputs.forEach(input => {
        const value = input.value.replace('Rp ', '').replace(/\./g, '');
        grandTotal += parseFloat(value) || 0;
    });
    
    document.getElementById('grandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});
</script>
@endsection 