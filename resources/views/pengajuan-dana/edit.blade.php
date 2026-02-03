@extends('layouts.app')

@section('title', 'Edit Pengajuan Dana - Ciwidey Agro Farm')

@section('page-title', 'Edit Pengajuan Dana')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Edit Pengajuan Dana</h2>
                <p class="text-gray-600 mt-1">Edit pengajuan dana bulanan untuk divisi peternakan atau perkebunan</p>
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
            <h3 class="text-lg font-semibold text-gray-800">Edit Data Pengajuan Dana</h3>
        </div>
        
        <form action="{{ route('admin.pengajuan-dana.update', $pengajuanDana->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $pengajuanDana->tanggal ? $pengajuanDana->tanggal->format('Y-m-d') : '') }}" required
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
                        <option value="peternakan" {{ old('divisi', $pengajuanDana->divisi) == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                        <option value="perkebunan" {{ old('divisi', $pengajuanDana->divisi) == 'perkebunan' ? 'selected' : '' }}>Perkebunan</option>
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
                        <option value="1" {{ old('bulan', $pengajuanDana->bulan) == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ old('bulan', $pengajuanDana->bulan) == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ old('bulan', $pengajuanDana->bulan) == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ old('bulan', $pengajuanDana->bulan) == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ old('bulan', $pengajuanDana->bulan) == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ old('bulan', $pengajuanDana->bulan) == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ old('bulan', $pengajuanDana->bulan) == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ old('bulan', $pengajuanDana->bulan) == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ old('bulan', $pengajuanDana->bulan) == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ old('bulan', $pengajuanDana->bulan) == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ old('bulan', $pengajuanDana->bulan) == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ old('bulan', $pengajuanDana->bulan) == 12 ? 'selected' : '' }}>Desember</option>
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
                <input type="number" name="tahun" id="tahun" value="{{ old('tahun', $pengajuanDana->tahun) }}" required min="2020" max="2030"
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

                <div id="items-container" class="space-y-4">
                    @foreach($pengajuanDana->items as $index => $item)
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="font-medium text-gray-800">Item #{{ $index + 1 }}</h5>
                            <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kebutuhan</label>
                                <select name="items[{{ $index }}][jenis_kebutuhan]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                                    <option value="">Pilih Jenis</option>
                                    <option value="operasional" {{ $item->jenis_kebutuhan == 'operasional' ? 'selected' : '' }}>Operasional</option>
                                    <option value="gaji" {{ $item->jenis_kebutuhan == 'gaji' ? 'selected' : '' }}>Gaji</option>
                                    <option value="konsumsi" {{ $item->jenis_kebutuhan == 'konsumsi' ? 'selected' : '' }}>Konsumsi</option>
                                    <option value="lainnya" {{ $item->jenis_kebutuhan == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kebutuhan</label>
                                <input type="text" name="items[{{ $index }}][nama_kebutuhan]" value="{{ $item->nama_kebutuhan }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                                    placeholder="Nama kebutuhan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                                <input type="number" name="items[{{ $index }}][jumlah]" value="{{ $item->jumlah }}" required step="0.01" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                                    placeholder="Jumlah" onchange="calculateTotal(this)">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                                <input type="text" name="items[{{ $index }}][satuan]" value="{{ $item->satuan }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                                    placeholder="Satuan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                                <input type="number" name="items[{{ $index }}][harga_satuan]" value="{{ $item->harga_satuan }}" required min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                                    placeholder="Harga" onchange="calculateTotal(this)">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                                <input type="text" readonly value="Rp {{ number_format($item->getTotalAmount(), 0, ',', '.') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea name="items[{{ $index }}][keterangan]" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                                placeholder="Keterangan (opsional)">{{ $item->keterangan }}</textarea>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">Informasi Edit</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                • Anda dapat menambah, mengedit, atau menghapus item kebutuhan<br>
                                • Setelah selesai, klik "Update Pengajuan" untuk menyimpan perubahan<br>
                                • Pengajuan akan tetap dalam status Draft sampai dikirim untuk approval
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
                    placeholder="Masukkan keterangan tambahan (opsional)">{{ old('keterangan', $pengajuanDana->keterangan) }}</textarea>
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
                    Update Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = {{ count($pengajuanDana->items) }};

function addItem() {
    const container = document.getElementById('items-container');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'bg-gray-50 p-4 rounded-lg border';
    itemDiv.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h5 class="font-medium text-gray-800">Item #${itemIndex + 1}</h5>
            <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kebutuhan</label>
                <select name="items[${itemIndex}][jenis_kebutuhan]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent">
                    <option value="">Pilih Jenis</option>
                    <option value="operasional">Operasional</option>
                    <option value="gaji">Gaji</option>
                    <option value="konsumsi">Konsumsi</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kebutuhan</label>
                <input type="text" name="items[${itemIndex}][nama_kebutuhan]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                    placeholder="Nama kebutuhan">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                <input type="number" name="items[${itemIndex}][jumlah]" required step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                    placeholder="Jumlah" onchange="calculateTotal(this)">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                <input type="text" name="items[${itemIndex}][satuan]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                    placeholder="Satuan">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                <input type="number" name="items[${itemIndex}][harga_satuan]" required min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                    placeholder="Harga" onchange="calculateTotal(this)">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                <input type="text" readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100"
                    placeholder="Total">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
            <textarea name="items[${itemIndex}][keterangan]" rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent"
                placeholder="Keterangan (opsional)"></textarea>
        </div>
    `;
    container.appendChild(itemDiv);
    itemIndex++;
}

function removeItem(button) {
    button.closest('.bg-gray-50').remove();
}

function calculateTotal(input) {
    const itemDiv = input.closest('.bg-gray-50');
    const jumlahInput = itemDiv.querySelector('input[name*="[jumlah]"]');
    const hargaInput = itemDiv.querySelector('input[name*="[harga_satuan]"]');
    const totalInput = itemDiv.querySelector('input[readonly]');
    
    const jumlah = parseFloat(jumlahInput.value) || 0;
    const harga = parseFloat(hargaInput.value) || 0;
    const total = jumlah * harga;
    
    totalInput.value = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endsection 