@extends('layouts.app')

@section('title', 'Edit Laporan Realisasi - Ciwidey Agro Farm')

@section('page-title', 'Edit Laporan Realisasi')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Edit Laporan Realisasi</h2>
                <p class="text-gray-600 mt-1">Edit laporan realisasi penggunaan dana mingguan</p>
            </div>
            <a href="{{ route('admin.laporan-realisasi.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Edit Data Laporan Realisasi</h3>
        </div>
        
        <form action="{{ route('admin.laporan-realisasi.update-advanced', $laporanRealisasi->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" id="laporanForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $laporanRealisasi->tanggal ? $laporanRealisasi->tanggal->format('Y-m-d') : date('Y-m-d')) }}" required
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
                        <option value="peternakan" {{ old('divisi', $laporanRealisasi->divisi) == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                        <option value="perkebunan" {{ old('divisi', $laporanRealisasi->divisi) == 'perkebunan' ? 'selected' : '' }}>Perkebunan</option>
                    </select>
                    @error('divisi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="minggu" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-week mr-2"></i>Minggu
                    </label>
                    <select name="minggu" id="minggu" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="">Pilih Minggu</option>
                        <option value="1" {{ old('minggu', $laporanRealisasi->minggu) == 1 ? 'selected' : '' }}>Week 1</option>
                        <option value="2" {{ old('minggu', $laporanRealisasi->minggu) == 2 ? 'selected' : '' }}>Week 2</option>
                        <option value="3" {{ old('minggu', $laporanRealisasi->minggu) == 3 ? 'selected' : '' }}>Week 3</option>
                        <option value="4" {{ old('minggu', $laporanRealisasi->minggu) == 4 ? 'selected' : '' }}>Week 4</option>
                    </select>
                    @error('minggu')
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
                        <option value="1" {{ old('bulan', $laporanRealisasi->bulan) == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ old('bulan', $laporanRealisasi->bulan) == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ old('bulan', $laporanRealisasi->bulan) == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ old('bulan', $laporanRealisasi->bulan) == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ old('bulan', $laporanRealisasi->bulan) == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ old('bulan', $laporanRealisasi->bulan) == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ old('bulan', $laporanRealisasi->bulan) == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ old('bulan', $laporanRealisasi->bulan) == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ old('bulan', $laporanRealisasi->bulan) == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ old('bulan', $laporanRealisasi->bulan) == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ old('bulan', $laporanRealisasi->bulan) == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ old('bulan', $laporanRealisasi->bulan) == 12 ? 'selected' : '' }}>Desember</option>
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
                <input type="number" name="tahun" id="tahun" value="{{ old('tahun', $laporanRealisasi->tahun) }}" required min="2020" max="2030"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                @error('tahun')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Edit Existing Items Section -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Edit Item Realisasi yang Sudah Ada</h4>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Sesuaikan jumlah dan harga realisasi
                    </div>
                </div>

                @if($laporanRealisasi->items->count() > 0)
                    <div class="space-y-4">
                        @foreach($laporanRealisasi->items as $index => $item)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                                        <div class="text-sm text-gray-900 font-medium">{{ $item->nama_item }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst($item->kategori) }}</div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengajuan vs Realisasi</label>
                                        <div class="text-xs text-gray-600">
                                            @if($item->pengajuanDanaItem)
                                                Pengajuan: {{ number_format(round($item->pengajuanDanaItem->jumlah), 0, ',', '.') }} {{ $item->pengajuanDanaItem->satuan }} × Rp {{ number_format(round($item->pengajuanDanaItem->harga_satuan), 0, ',', '.') }}
                                            @else
                                                <span class="text-orange-600">Item baru (tidak ada di pengajuan)</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="existing_items_{{ $index }}_jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Realisasi</label>
                                        <input type="number" 
                                               name="existing_items[{{ $index }}][jumlah_realisasi]" 
                                               id="existing_items_{{ $index }}_jumlah"
                                               value="{{ round($item->jumlah) }}" 
                                               step="1" 
                                               min="0"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm">
                                        <input type="hidden" name="existing_items[{{ $index }}][id]" value="{{ $item->id }}">
                                    </div>
                                    
                                    <div>
                                        <label for="existing_items_{{ $index }}_harga" class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan Realisasi</label>
                                        <input type="text" 
                                               name="existing_items[{{ $index }}][harga_satuan_realisasi]" 
                                               id="existing_items_{{ $index }}_harga"
                                               value="{{ number_format((int) round($item->harga_satuan), 0, ',', '.') }}" 
                                               inputmode="numeric" pattern="[0-9\.]*"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                                               oninput="formatCurrencyDots(this); calculateExistingItemTotal(this)">
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <label for="existing_items_{{ $index }}_keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <input type="text" 
                                           name="existing_items[{{ $index }}][keterangan]" 
                                           id="existing_items_{{ $index }}_keterangan"
                                           value="{{ $item->keterangan }}" 
                                           placeholder="Keterangan tambahan..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm">
                                </div>
                                
                                <div class="mt-2 text-sm">
                                    <span class="text-gray-600">Total Realisasi: </span>
                                    <span class="font-semibold text-green-600" id="total_{{ $index }}">
                                        Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada item realisasi yang dapat diedit</p>
                    </div>
                @endif
            </div>

            <!-- Add New Items Section -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Tambah Item Baru (Pembelian Urgent)</h4>
                    <button type="button" onclick="addNewItem()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Item Baru
                    </button>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Pembelian Urgent</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                Gunakan fitur ini untuk menambah item yang tidak ada di pengajuan awal (misal: obat darurat, perbaikan mendadak). 
                                Item ini akan dihitung sebagai saldo minus dan diajukan kembali bulan berikutnya.
                            </p>
                        </div>
                    </div>
                </div>

                <div id="new-items-container" class="space-y-4">
                    <!-- New items will be added here -->
                </div>
            </div>

            <!-- Balance Calculation Section -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Perhitungan Saldo</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <h5 class="text-sm font-semibold text-green-800">Total Pengajuan</h5>
                            <div class="text-lg font-bold text-green-800" id="totalPengajuan">Rp 0</div>
                        </div>
                        <div class="text-xs text-green-600 mt-1">Jumlah yang diajukan</div>
                    </div>
                    
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <h5 class="text-sm font-semibold text-blue-800">Total Realisasi</h5>
                            <div class="text-lg font-bold text-blue-800" id="totalRealisasi">Rp 0</div>
                        </div>
                        <div class="text-xs text-blue-600 mt-1">Jumlah yang digunakan</div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <h5 class="text-sm font-semibold text-yellow-800">Sisa Saldo</h5>
                            <div class="text-lg font-bold text-yellow-800" id="sisaSaldo">Rp 0</div>
                        </div>
                        <div class="text-xs text-yellow-600 mt-1">Pengajuan > Realisasi</div>
                    </div>
                    
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <h5 class="text-sm font-semibold text-red-800">Saldo Minus</h5>
                            <div class="text-lg font-bold text-red-800" id="saldoMinus">Rp 0</div>
                        </div>
                        <div class="text-xs text-red-600 mt-1">Realisasi > Pengajuan (akan diajukan kembali)</div>
                    </div>
                </div>
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                    placeholder="Masukkan keterangan tambahan (opsional)">{{ old('keterangan', $laporanRealisasi->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.laporan-realisasi.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Update Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function formatCurrencyDots(input) {
    let raw = (input.value || '').toString().replace(/[^\d]/g, '');
    if (raw === '') { input.value = ''; return; }
    input.value = parseInt(raw, 10).toLocaleString('id-ID').replace(/,/g, '.');
}

let newItemIndex = 0;

// Function to add new item (urgent purchases)
function addNewItem() {
    const container = document.getElementById('new-items-container');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'bg-white p-4 rounded-lg border border-gray-200 shadow-sm';
    itemDiv.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="new_items[${newItemIndex}][kategori]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm">
                    <option value="">Pilih Kategori</option>
                    <option value="pendapatan">Pendapatan</option>
                    <option value="tenaga_konsumsi">Tenaga Kerja & Konsumsi</option>
                    <option value="alat_bahan">Alat & Bahan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                <input type="text" name="new_items[${newItemIndex}][nama_item]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="Nama item">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                <input type="number" name="new_items[${newItemIndex}][jumlah]" required step="1" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="Jumlah" onchange="calculateNewItemTotal(this)">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                <input type="text" name="new_items[${newItemIndex}][satuan]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="Satuan">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                <input type="text" name="new_items[${newItemIndex}][harga_satuan]" required min="0" inputmode="numeric" pattern="[0-9\.]*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="Harga" oninput="formatCurrencyDots(this); calculateNewItemTotal(this)">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                <input type="text" readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm font-semibold"
                    placeholder="Total">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pembelian Urgent</label>
                <div class="flex items-center mt-2">
                    <input type="checkbox" name="new_items[${newItemIndex}][is_urgent]" value="1" checked
                        class="mr-2 text-agro-green focus:ring-agro-green">
                    <span class="text-sm text-gray-600">Ya (akan jadi saldo minus)</span>
                </div>
            </div>
            
            <div class="flex items-end">
                <button type="button" onclick="removeNewItem(this)" class="bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition-colors text-sm">
                    <i class="fas fa-trash mr-1"></i>
                    Hapus
                </button>
            </div>
        </div>
        
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <input type="text" name="new_items[${newItemIndex}][keterangan]"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                placeholder="Keterangan tambahan...">
        </div>
    `;
    container.appendChild(itemDiv);
    newItemIndex++;
    updateBalanceCalculation();
}

// Function to remove new item
function removeNewItem(button) {
    button.closest('.bg-white').remove();
    updateBalanceCalculation();
}

// Function to calculate total for new item
function calculateNewItemTotal(input) {
    const itemDiv = input.closest('.bg-white');
    const jumlahInput = itemDiv.querySelector('input[name*="[jumlah]"]');
    const hargaInput = itemDiv.querySelector('input[name*="[harga_satuan]"]');
    const totalInput = itemDiv.querySelector('input[readonly]');
    
    const jumlah = parseFloat(jumlahInput.value) || 0;
    const harga = parseInt((hargaInput.value || '').toString().replace(/\./g, ''), 10) || 0;
    const total = jumlah * harga;
    
    totalInput.value = total > 0 ? 'Rp ' + total.toLocaleString('id-ID').replace(/,/g, '.') : '';
    updateBalanceCalculation();
}

// Function to calculate total for existing item
function calculateExistingItemTotal(input) {
    const itemDiv = input.closest('.bg-gray-50');
    const jumlahInput = itemDiv.querySelector('input[name*="[jumlah_realisasi]"]');
    const hargaInput = itemDiv.querySelector('input[name*="[harga_satuan_realisasi]"]');
    const totalSpan = itemDiv.querySelector('span[id^="total_"]');
    
    const jumlah = parseFloat(jumlahInput.value) || 0;
    const harga = parseInt((hargaInput.value || '').toString().replace(/\./g, ''), 10) || 0;
    const total = jumlah * harga;
    
    totalSpan.textContent = 'Rp ' + total.toLocaleString('id-ID').replace(/,/g, '.');
    updateBalanceCalculation();
}

// Function to update balance calculation
function updateBalanceCalculation() {
    let totalPengajuan = 0;
    let totalRealisasi = 0;
    
    // Calculate from existing items
    const existingItems = document.querySelectorAll('.bg-gray-50');
    existingItems.forEach(item => {
        const jumlahInput = item.querySelector('input[name*="[jumlah_realisasi]"]');
        const hargaInput = item.querySelector('input[name*="[harga_satuan_realisasi]"]');
        
        if (jumlahInput && hargaInput) {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const harga = parseInt((hargaInput.value || '').toString().replace(/\./g, ''), 10) || 0;
            totalRealisasi += (jumlah * harga);
        }
    });
    
    // Calculate from new items
    const newItems = document.querySelectorAll('#new-items-container .bg-white');
    newItems.forEach(item => {
        const jumlahInput = item.querySelector('input[name*="[jumlah]"]');
        const hargaInput = item.querySelector('input[name*="[harga_satuan]"]');
        
        if (jumlahInput && hargaInput) {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const harga = parseInt((hargaInput.value || '').toString().replace(/\./g, ''), 10) || 0;
            totalRealisasi += (jumlah * harga);
        }
    });
    
    // Calculate saldo
    const saldo = totalPengajuan - totalRealisasi;
    const sisaSaldo = saldo > 0 ? saldo : 0;
    const saldoMinus = saldo < 0 ? Math.abs(saldo) : 0;
    
    // Update display
    document.getElementById('totalPengajuan').textContent = 'Rp ' + totalPengajuan.toLocaleString('id-ID').replace(/,/g, '.');
    document.getElementById('totalRealisasi').textContent = 'Rp ' + totalRealisasi.toLocaleString('id-ID').replace(/,/g, '.');
    document.getElementById('sisaSaldo').textContent = 'Rp ' + sisaSaldo.toLocaleString('id-ID').replace(/,/g, '.');
    document.getElementById('saldoMinus').textContent = 'Rp ' + saldoMinus.toLocaleString('id-ID').replace(/,/g, '.');
}

// Add event listeners for existing items
document.addEventListener('DOMContentLoaded', function() {
    // Add change event listeners to existing item inputs
    const existingInputs = document.querySelectorAll('input[name*="[jumlah_realisasi]"], input[name*="[harga_satuan_realisasi]"]');
    existingInputs.forEach(input => {
        input.addEventListener('input', calculateExistingItemTotal);
    });
    
    // Initial calculation
    updateBalanceCalculation();
});
</script>
@endsection 