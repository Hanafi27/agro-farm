@extends('layouts.app')

@section('title', 'Tambah Laporan Realisasi - Ciwidey Agro Farm')

@section('page-title', 'Tambah Laporan Realisasi')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tambah Laporan Realisasi Baru</h2>
                <p class="text-gray-600 mt-1">Pilih pengajuan dana yang sudah disetujui untuk membuat laporan realisasi</p>
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
            <h3 class="text-lg font-semibold text-gray-800">Data Laporan Realisasi</h3>
        </div>
        
        <form action="{{ route('admin.laporan-realisasi.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" id="laporanForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                    <select name="divisi" id="divisi" required onchange="showDivisiItems(); updateDropdownStates()"
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
                    <label for="minggu" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-week mr-2"></i>Minggu <span class="text-gray-500 text-xs">(Opsional)</span>
                    </label>
                    <select name="minggu" id="minggu" disabled
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="">Pilih Minggu (Opsional - akan ditentukan otomatis)</option>
                        <option value="1" {{ old('minggu', $currentWeek) == 1 ? 'selected' : '' }}>Week 1</option>
                        <option value="2" {{ old('minggu', $currentWeek) == 2 ? 'selected' : '' }}>Week 2</option>
                        <option value="3" {{ old('minggu', $currentWeek) == 3 ? 'selected' : '' }}>Week 3</option>
                        <option value="4" {{ old('minggu', $currentWeek) == 4 ? 'selected' : '' }}>Week 4</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Jika tidak dipilih, minggu akan ditentukan otomatis berdasarkan tanggal laporan</p>
                    @error('minggu')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2"></i>Bulan
                    </label>
                    <select name="bulan" id="bulan" required disabled
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
            </div>

            <!-- Pengajuan Selection Section -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Pilih Pengajuan yang Disetujui</h4>
                    <div class="flex space-x-2">
                        <button type="button" onclick="selectAllPeternakan()" class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm">
                            Pilih Semua Peternakan
                        </button>
                        <button type="button" onclick="selectAllPerkebunan()" class="bg-green-500 text-white px-3 py-2 rounded-lg hover:bg-green-600 transition-colors text-sm">
                            Pilih Semua Perkebunan
                        </button>
                        <button type="button" onclick="clearAll()" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 transition-colors text-sm">
                            Hapus Semua
                        </button>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">Cara Membuat Laporan Realisasi</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                1. Pilih divisi (Peternakan atau Perkebunan)<br>
                                2. Pilih bulan dan tahun (minggu bersifat opsional - akan ditentukan otomatis)<br>
                                3. Pilih pengajuan dana yang sudah disetujui dan dana diberikan<br>
                                4. Pilih pendapatan yang ingin dimasukkan ke laporan<br>
                                5. Klik "Simpan Laporan" untuk membuat draft laporan realisasi
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Peternakan Section -->
                <div id="peternakan-section" class="mb-6" style="display: none;">
                    <h5 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                        <i class="fas fa-cow mr-2"></i>
                        Pengajuan Peternakan yang Disetujui
                    </h5>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        @if($peternakanPengajuan->count() > 0)
                            <div class="space-y-3">
                                @foreach($peternakanPengajuan as $pengajuan)
                                    <div class="bg-white p-4 rounded-lg border border-blue-200">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <h6 class="font-semibold text-gray-800">
                                                    Pengajuan #{{ $pengajuan->id }} - {{ \Carbon\Carbon::createFromDate($pengajuan->tahun, $pengajuan->bulan, 1)->format('F Y') }}
                                                </h6>
                                                <p class="text-sm text-gray-600">
                                                    Oleh: {{ $pengajuan->submittedBy->name ?? 'N/A' }} | 
                                                    Status: {{ $pengajuan->getStatusLabel() }} |
                                                    Total: Rp {{ number_format($pengajuan->getTotalAmount(), 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <button type="button" onclick="togglePengajuanItems({{ $pengajuan->id }})" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-chevron-down" id="icon-{{ $pengajuan->id }}"></i>
                                            </button>
                                        </div>
                                        <div id="items-{{ $pengajuan->id }}" class="hidden">
                                            <div class="space-y-3">
                                                @foreach($pengajuan->items as $item)
                                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                                        <div class="flex items-center space-x-3 mb-3">
                                                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" 
                                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                                onchange="toggleItemEdit({{ $item->id }}, this.checked); updateSelectedCount()">
                                                            <div class="flex-1">
                                                                <span class="font-medium">{{ $item->nama_kebutuhan }}</span>
                                                                <span class="text-sm text-gray-600 ml-2">
                                                                    ({{ $item->jenis_kebutuhan }})
                                                                </span>
                                                            </div>
                                                            <div class="text-sm text-gray-600">
                                                                Pengajuan: {{ $item->jumlah }} {{ $item->satuan }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                                            </div>
                                                            <div class="text-sm font-medium text-green-600">
                                                                Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Edit Fields (Hidden by default) -->
                                                        <div id="edit-fields-{{ $item->id }}" class="hidden bg-white p-3 rounded border border-blue-200">
                                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Realisasi</label>
                                                                    <input type="text" 
                                                                           name="item_realisasi[{{ $item->id }}][jumlah]" 
                                                                           value="{{ number_format(round($item->jumlah), 0, ',', '') }}"
                                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                                           placeholder="0"
                                                                           oninput="formatInteger(this); calculateItemTotal({{ $item->id }})"
                                                                           onblur="validateInteger(this)">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan Realisasi</label>
                                                                    <input type="text" 
                                                                           name="item_realisasi[{{ $item->id }}][harga_satuan]" 
                                                                           value="{{ number_format(round($item->harga_satuan), 0, ',', '.') }}"
                                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                                           placeholder="0"
                                                                           oninput="formatCurrency(this); calculateItemTotal({{ $item->id }})"
                                                                           onblur="validateCurrency(this)">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Realisasi</label>
                                                                    <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm font-semibold text-green-600" id="total-{{ $item->id }}">
                                                                        Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mt-3">
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Realisasi</label>
                                                                <input type="text" 
                                                                       name="item_realisasi[{{ $item->id }}][keterangan]"
                                                                       placeholder="Keterangan tambahan..."
                                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                                            </div>
                                                            
                                                            <div class="mt-2 text-xs text-gray-600">
                                                                <span class="text-blue-600">Pengajuan:</span> Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }} | 
                                                                <span class="text-green-600">Realisasi:</span> <span id="realisasi-{{ $item->id }}">Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Tidak ada pengajuan peternakan yang disetujui</p>
                        @endif
                    </div>
                </div>

                <!-- Perkebunan Section -->
                <div id="perkebunan-section" class="mb-6" style="display: none;">
                    <h5 class="text-lg font-semibold text-green-800 mb-3 flex items-center">
                        <i class="fas fa-seedling mr-2"></i>
                        Pengajuan Perkebunan yang Disetujui
                    </h5>
                    <div class="bg-green-50 p-4 rounded-lg">
                        @if($perkebunanPengajuan->count() > 0)
                            <div class="space-y-3">
                                @foreach($perkebunanPengajuan as $pengajuan)
                                    <div class="bg-white p-4 rounded-lg border border-green-200">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <h6 class="font-semibold text-gray-800">
                                                    Pengajuan #{{ $pengajuan->id }} - {{ \Carbon\Carbon::createFromDate($pengajuan->tahun, $pengajuan->bulan, 1)->format('F Y') }}
                                                </h6>
                                                <p class="text-sm text-gray-600">
                                                    Oleh: {{ $pengajuan->submittedBy->name ?? 'N/A' }} | 
                                                    Status: {{ $pengajuan->getStatusLabel() }} |
                                                    Total: Rp {{ number_format($pengajuan->getTotalAmount(), 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <button type="button" onclick="togglePengajuanItems({{ $pengajuan->id }})" class="text-green-600 hover:text-green-800">
                                                <i class="fas fa-chevron-down" id="icon-{{ $pengajuan->id }}"></i>
                                            </button>
                                        </div>
                                        <div id="items-{{ $pengajuan->id }}" class="hidden">
                                            <div class="space-y-3">
                                                @foreach($pengajuan->items as $item)
                                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                                        <div class="flex items-center space-x-3 mb-3">
                                                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" 
                                                                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                                                onchange="toggleItemEdit({{ $item->id }}, this.checked); updateSelectedCount()">
                                                            <div class="flex-1">
                                                                <span class="font-medium">{{ $item->nama_kebutuhan }}</span>
                                                                <span class="text-sm text-gray-600 ml-2">
                                                                    ({{ $item->jenis_kebutuhan }})
                                                                </span>
                                                            </div>
                                                            <div class="text-sm text-gray-600">
                                                                Pengajuan: {{ $item->jumlah }} {{ $item->satuan }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                                            </div>
                                                            <div class="text-sm font-medium text-green-600">
                                                                Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Edit Fields (Hidden by default) -->
                                                        <div id="edit-fields-{{ $item->id }}" class="hidden bg-white p-3 rounded border border-green-200">
                                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Realisasi</label>
                                                                    <input type="text" 
                                                                           name="item_realisasi[{{ $item->id }}][jumlah]" 
                                                                           value="{{ number_format(round($item->jumlah), 0, ',', '') }}"
                                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                                                           placeholder="0"
                                                                           oninput="formatInteger(this); calculateItemTotal({{ $item->id }})"
                                                                           onblur="validateInteger(this)">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan Realisasi</label>
                                                                    <input type="text" 
                                                                           name="item_realisasi[{{ $item->id }}][harga_satuan]" 
                                                                           value="{{ number_format(round($item->harga_satuan), 0, ',', '.') }}"
                                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                                                           placeholder="0"
                                                                           oninput="formatCurrency(this); calculateItemTotal({{ $item->id }})"
                                                                           onblur="validateCurrency(this)">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Realisasi</label>
                                                                    <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm font-semibold text-green-600" id="total-{{ $item->id }}">
                                                                        Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mt-3">
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Realisasi</label>
                                                                <input type="text" 
                                                                       name="item_realisasi[{{ $item->id }}][keterangan]"
                                                                       placeholder="Keterangan tambahan..."
                                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                                                            </div>
                                                            
                                                            <div class="mt-2 text-xs text-gray-600">
                                                                <span class="text-blue-600">Pengajuan:</span> Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }} | 
                                                                <span class="text-green-600">Realisasi:</span> <span id="realisasi-{{ $item->id }}">Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Tidak ada pengajuan perkebunan yang disetujui</p>
                        @endif
                    </div>
                </div>

                <!-- Pendapatan Section -->
                <div id="pendapatan-section" class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h5 class="text-lg font-semibold text-yellow-800 flex items-center">
                            <i class="fas fa-coins mr-2"></i>
                            Pendapatan Bulan Ini
                        </h5>
                        <button type="button" onclick="selectAllPendapatan()" class="bg-yellow-500 text-white px-3 py-2 rounded-lg hover:bg-yellow-600 transition-colors text-sm">
                            Pilih Semua Pendapatan
                        </button>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        @php
                            $bulan = old('bulan', $currentMonth);
                            $tahun = old('tahun', $currentYear);
                            $divisi = old('divisi', 'peternakan');
                            $pendapatanList = \App\Models\PendapatanSusu::whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->where('kategori', $divisi)
                                ->get()
                                ->groupBy('jenis_produk');
                        @endphp
                        @if($pendapatanList->count() > 0)
                            <div class="space-y-3">
                                @foreach($pendapatanList as $jenis_produk => $pendapatans)
                                    @php
                                        $total_liter = $pendapatans->sum('jumlah_liter');
                                        $avg_harga = $pendapatans->avg('harga_per_liter');
                                        $total_pendapatan = $pendapatans->sum('total_pendapatan');
                                        $satuan = $pendapatans->first()->satuan ?? 'liter';
                                    @endphp
                                    <div class="flex items-center space-x-3 p-2 bg-white rounded border border-yellow-200">
                                        <input type="checkbox" name="selected_pendapatan[]" value="{{ $jenis_produk }}"
                                            class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500" onchange="updateSelectedCount()">
                                        <div class="flex-1">
                                            <span class="font-medium">Pendapatan {{ ucfirst(str_replace('_', ' ', $jenis_produk)) }}</span>
                                            <span class="text-sm text-gray-600 ml-2">({{ $satuan }})</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ number_format($total_liter, 2, ',', '.') }} {{ $satuan }} × Rp {{ number_format($avg_harga, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm font-medium text-green-600">
                                            Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Tidak ada data pendapatan untuk bulan ini</p>
                        @endif
                    </div>
                </div>

                <!-- Add New Items Section -->
                <div class="border-t border-gray-200 pt-6 mb-6">
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
                <div class="border-t border-gray-200 pt-6 mb-6">
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

                <!-- Selected Items Summary -->
                <div id="selected-summary" class="bg-yellow-50 p-4 rounded-lg border border-yellow-200" style="display: none;">
                    <h5 class="text-lg font-semibold text-yellow-800 mb-2">Item yang Dipilih</h5>
                    <p class="text-sm text-yellow-700">
                        <span id="selected-count">0</span> item dipilih dengan total nilai 
                        <span id="selected-total" class="font-semibold">Rp 0</span>
                    </p>
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

            <!-- Attachment Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-paperclip mr-2"></i>Lampiran Bukti Transaksi
                </label>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">Upload Bukti Transaksi</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Unggah 1 file bukti transaksi untuk item yang dipilih. Format didukung: JPG, PNG, WEBP, PDF, DOC/DOCX, XLS/XLSX, CSV, atau TXT. Batas ukuran file 20 MB. (Opsional)
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Attachment Fields Container -->
                <div id="attachment-container" class="mt-4 space-y-4">
                    <!-- Attachment fields will be dynamically added here -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.laporan-realisasi.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showDivisiItems() {
    const divisi = document.getElementById('divisi').value;
    const peternakanSection = document.getElementById('peternakan-section');
    const perkebunanSection = document.getElementById('perkebunan-section');
    
    peternakanSection.style.display = 'none';
    perkebunanSection.style.display = 'none';
    
    if (divisi === 'peternakan') {
        peternakanSection.style.display = 'block';
    } else if (divisi === 'perkebunan') {
        perkebunanSection.style.display = 'block';
    }
}

function togglePengajuanItems(pengajuanId) {
    const itemsDiv = document.getElementById(`items-${pengajuanId}`);
    const icon = document.getElementById(`icon-${pengajuanId}`);
    
    if (itemsDiv.classList.contains('hidden')) {
        itemsDiv.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        itemsDiv.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

function selectAllPeternakan() {
    const checkboxes = document.querySelectorAll('#peternakan-section input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
}

function selectAllPerkebunan() {
    const checkboxes = document.querySelectorAll('#perkebunan-section input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
}

function selectAllPendapatan() {
    const checkboxes = document.querySelectorAll('#pendapatan-section input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
}

function clearAll() {
    const checkboxes = document.querySelectorAll('input[name="selected_items[]"], input[name="selected_pendapatan[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const pengajuanCheckboxes = document.querySelectorAll('input[name="selected_items[]"]:checked');
    const pendapatanCheckboxes = document.querySelectorAll('input[name="selected_pendapatan[]"]:checked');
    const totalSelected = pengajuanCheckboxes.length + pendapatanCheckboxes.length;
    
    const summary = document.getElementById('selected-summary');
    const countSpan = document.getElementById('selected-count');
    const totalSpan = document.getElementById('selected-total');
    
    if (totalSelected > 0) {
        summary.style.display = 'block';
        countSpan.textContent = totalSelected;
        
        // Calculate total (this would need to be enhanced with actual item data)
        totalSpan.textContent = 'Rp ' + (totalSelected * 1000000).toLocaleString('id-ID').replace(/,/g, '.');
    } else {
        summary.style.display = 'none';
    }
    
    // Update attachment fields
    updateAttachmentFields();
}

function updateAttachmentFields() {
    const container = document.getElementById('attachment-container');
    container.innerHTML = '';
    
    const selectedItems = document.querySelectorAll('input[name="selected_items[]"]:checked');
    const selectedPendapatan = document.querySelectorAll('input[name="selected_pendapatan[]"]:checked');
    const tanggalTransaksi = (document.getElementById('tanggal') && document.getElementById('tanggal').value) ? document.getElementById('tanggal').value : '';
    
    // Add attachment fields for selected pengajuan items
    selectedItems.forEach((item, index) => {
        const itemDiv = item.closest('.bg-white');
        const itemName = itemDiv.querySelector('.font-medium').textContent.trim();
        const itemValue = itemDiv.querySelector('.text-green-600').textContent.trim();
        
        const attachmentDiv = document.createElement('div');
        attachmentDiv.className = 'bg-white p-4 rounded-lg border border-gray-200';
        attachmentDiv.innerHTML = `
            <div class="mb-3">
                <p class="text-xs text-gray-500">Bukti transaksi realisasi</p>
                <div class="text-sm text-gray-800"><span class="font-medium text-gray-600">Tanggal Transaksi</span>: ${tanggalTransaksi || '-'}</div>
                <div class="text-sm text-gray-800"><span class="font-medium text-gray-600">Nilai Realisasi</span> (Jumlah × Harga): ${itemValue}</div>
            </div>
            <div>
                <input type="file" name="item_attachments[${item.value}][]" multiple
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2" 
                    accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.csv,.txt">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WEBP, PDF, DOC/DOCX, XLS/XLSX, CSV, TXT (maks. 20 MB). Opsional.</p>
            </div>
        `;
        container.appendChild(attachmentDiv);
    });
    
    // Add attachment fields for selected pendapatan items
    selectedPendapatan.forEach((item, index) => {
        const itemDiv = item.closest('.bg-white');
        const itemName = itemDiv.querySelector('.font-medium').textContent.trim();
        const itemValue = itemDiv.querySelector('.text-green-600').textContent.trim();
        
        const attachmentDiv = document.createElement('div');
        attachmentDiv.className = 'bg-white p-4 rounded-lg border border-gray-200';
        attachmentDiv.innerHTML = `
            <div class="mb-3">
                <p class="text-xs text-gray-500">Bukti transaksi realisasi</p>
                <div class="text-sm text-gray-800"><span class="font-medium text-gray-600">Tanggal Transaksi</span>: ${tanggalTransaksi || '-'}</div>
                <div class="text-sm text-gray-800"><span class="font-medium text-gray-600">Nilai Realisasi</span> (Jumlah × Harga): ${itemValue}</div>
            </div>
            <div>
                <input type="file" name="pendapatan_attachments[${item.value}][]" multiple
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2" 
                    accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.csv,.txt">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WEBP, PDF, DOC/DOCX, XLS/XLSX, CSV, TXT (maks. 20 MB). Opsional.</p>
            </div>
        `;
        container.appendChild(attachmentDiv);
    });
}

function updateDropdownStates() {
    const divisi = document.getElementById('divisi').value;
    const minggu = document.getElementById('minggu');
    const bulan = document.getElementById('bulan');
    
    if (divisi) {
        minggu.disabled = false; // Enable minggu dropdown but keep it optional
        bulan.disabled = false; // Enable bulan dropdown
    } else {
        minggu.disabled = true;
        minggu.value = '';
        bulan.disabled = true;
        bulan.value = '';
    }
}

function updateFormVisibility() {
    const divisiSelect = document.getElementById('divisi');
    const mingguSelect = document.getElementById('minggu');
    const bulanSelect = document.getElementById('bulan');
    const pendapatanSection = document.getElementById('pendapatan-section');

    // Reset and disable subsequent dropdowns
    mingguSelect.value = '';
    mingguSelect.disabled = true;
    bulanSelect.value = '';
    bulanSelect.disabled = false; // Enable bulan dropdown
    pendapatanSection.style.display = 'none';

    if (divisiSelect.value) {
        mingguSelect.disabled = false; // Enable minggu dropdown but keep it optional
        // Show pendapatan section based on divisi selection
        showPendapatanSection();
    }
}

// Function to fetch and display income data
function showPendapatanSection() {
    const divisi = document.getElementById('divisi').value;
    const bulan = document.getElementById('bulan').value;
    const tahun = document.getElementById('tahun').value;
    const pendapatanSection = document.getElementById('pendapatan-section');

    console.log('showPendapatanSection called:', { divisi, bulan, tahun });

    if (divisi && bulan && tahun) {
        const url = `/test-pendapatan?divisi=${divisi}&bulan=${bulan}&tahun=${tahun}`;
        console.log('Fetching from URL:', url);
        
        // Fetch pendapatan data via AJAX
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                if (!response.ok) {
                    if (response.status === 401 || response.status === 403) {
                        throw new Error('Anda harus login sebagai admin untuk mengakses data ini');
                    } else if (response.status === 404) {
                        throw new Error('Route tidak ditemukan. Silakan refresh halaman dan coba lagi');
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                }
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);
                    if (data.success && data.pendapatan.length > 0) {
                        let html = '<div class="space-y-3">';
                        data.pendapatan.forEach(item => {
                            html += `
                                <div class="flex items-center space-x-3 p-2 bg-white rounded border border-yellow-200">
                                    <input type="checkbox" name="selected_pendapatan[]" value="${item.id}" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500" onchange="updateSelectedCount()">
                                    <div class="flex-1">
                                        <span class="font-medium">Pendapatan ${item.jenis_produk.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                                        <span class="text-sm text-gray-600 ml-2">(${item.satuan})</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        ${parseFloat(item.jumlah_liter).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${item.satuan} × Rp ${parseFloat(item.harga_per_liter).toLocaleString('id-ID').replace(/,/g, '.')}
                                    </div>
                                    <div class="text-sm font-medium text-yellow-600">
                                        Rp ${parseFloat(item.total_pendapatan).toLocaleString('id-ID').replace(/,/g, '.')}
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        pendapatanSection.querySelector('.bg-yellow-50').innerHTML = html;
                    } else {
                        pendapatanSection.querySelector('.bg-yellow-50').innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada data pendapatan untuk periode ini.</p>';
                    }
                    pendapatanSection.style.display = 'block';
                } catch (e) {
                    console.error('JSON parse error:', e);
                    pendapatanSection.querySelector('.bg-yellow-50').innerHTML = '<p class="text-red-500 text-center py-4">Error parsing response: ' + e.message + '</p>';
                    pendapatanSection.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error fetching pendapatan:', error);
                let errorMessage = 'Error mengambil data pendapatan: ' + error.message;
                
                if (error.message.includes('401') || error.message.includes('403')) {
                    errorMessage = 'Anda harus login sebagai admin/owner/keuangan untuk mengakses data ini. Silakan refresh halaman dan login kembali.';
                } else if (error.message.includes('404')) {
                    errorMessage = 'Route tidak ditemukan. Silakan refresh halaman dan coba lagi.';
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Tidak dapat terhubung ke server. Pastikan server Laravel berjalan.';
                }
                
                pendapatanSection.querySelector('.bg-yellow-50').innerHTML = '<p class="text-red-500 text-center py-4">' + errorMessage + '</p>';
                pendapatanSection.style.display = 'block';
            });
    } else if (divisi) {
        // Show section but with message to select bulan and tahun
        pendapatanSection.querySelector('.bg-yellow-50').innerHTML = '<p class="text-gray-500 text-center py-4">Pilih bulan dan tahun untuk melihat pendapatan.</p>';
        pendapatanSection.style.display = 'block';
    } else {
        pendapatanSection.style.display = 'none';
    }
}

// Function to filter pengajuan dana based on bulan
function filterPengajuanDana() {
    const divisi = document.getElementById('divisi').value;
    const bulan = document.getElementById('bulan').value;
    const tahun = document.getElementById('tahun').value;
    
    if (divisi && bulan && tahun) {
        const url = `/test-pengajuan?divisi=${divisi}&bulan=${bulan}&tahun=${tahun}`;
        console.log('Fetching pengajuan dana from URL:', url);
        
        // Fetch pengajuan dana data via AJAX
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Pengajuan dana response status:', response.status);
                if (!response.ok) {
                    if (response.status === 401 || response.status === 403) {
                        throw new Error('Anda harus login sebagai admin untuk mengakses data ini');
                    } else if (response.status === 404) {
                        throw new Error('Route tidak ditemukan. Silakan refresh halaman dan coba lagi');
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                }
                return response.text();
            })
            .then(text => {
                console.log('Pengajuan dana response text:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Pengajuan dana parsed data:', data);
                    if (data.success && data.items.length > 0) {
                        // Update pengajuan dana sections
                        updatePengajuanSection('peternakan', data.items.filter(item => item.pengajuan_info.divisi === 'peternakan'));
                        updatePengajuanSection('perkebunan', data.items.filter(item => item.pengajuan_info.divisi === 'perkebunan'));
                    } else {
                        // Clear pengajuan sections
                        clearPengajuanSections();
                    }
                } catch (e) {
                    console.error('JSON parse error for pengajuan dana:', e);
                    clearPengajuanSections();
                }
            })
            .catch(error => {
                console.error('Error fetching pengajuan dana:', error);
                let errorMessage = 'Error mengambil data pengajuan dana: ' + error.message;
                
                if (error.message.includes('401') || error.message.includes('403')) {
                    errorMessage = 'Anda harus login sebagai admin/owner/keuangan untuk mengakses data ini. Silakan refresh halaman dan login kembali.';
                } else if (error.message.includes('404')) {
                    errorMessage = 'Route tidak ditemukan. Silakan refresh halaman dan coba lagi.';
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Tidak dapat terhubung ke server. Pastikan server Laravel berjalan.';
                }
                
                console.error(errorMessage);
                // Clear pengajuan sections
                clearPengajuanSections();
            });
    } else {
        clearPengajuanSections();
    }
}

function updatePengajuanSection(divisi, itemsList) {
    const section = document.getElementById(`${divisi}-section`);
    if (section) {
        if (itemsList.length > 0) {
            let html = '<div class="space-y-3">';
            itemsList.forEach(item => {
                html += `
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3 mb-3">
                            <input type="checkbox" name="selected_items[]" value="${item.id}" 
                                class="rounded border-gray-300 text-${divisi === 'peternakan' ? 'blue' : 'green'}-600 focus:ring-${divisi === 'peternakan' ? 'blue' : 'green'}-500"
                                onchange="toggleItemEdit(${item.id}, this.checked); updateSelectedCount()">
                            <div class="flex-1">
                                <span class="font-medium">${item.nama_kebutuhan}</span>
                                <span class="text-sm text-gray-600 ml-2">
                                    (${item.jenis_kebutuhan})
                                </span>
                                <div class="text-xs text-gray-500 mt-1">
                                    Pengajuan #${item.pengajuan_info.id} - Week ${item.pengajuan_info.minggu} 
                                    ${new Date(item.pengajuan_info.tahun, item.pengajuan_info.bulan - 1, 1).toLocaleDateString('id-ID', {month: 'long', year: 'numeric'})}
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                Pengajuan: ${item.jumlah} ${item.satuan} × Rp ${item.harga_satuan.toLocaleString('id-ID').replace(/,/g, '.')}
                            </div>
                            <div class="text-sm font-medium text-green-600">
                                Rp ${item.total_harga.toLocaleString('id-ID').replace(/,/g, '.')}
                            </div>
                        </div>
                        
                        <!-- Edit Fields (Hidden by default) -->
                        <div id="edit-fields-${item.id}" class="hidden bg-white p-3 rounded border border-${divisi === 'peternakan' ? 'blue' : 'green'}-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Realisasi</label>
                                    <input type="text" 
                                           name="item_realisasi[${item.id}][jumlah]" 
                                           value="${Math.round(item.jumlah).toLocaleString('id-ID').replace(/,/g, '.')}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-${divisi === 'peternakan' ? 'blue' : 'green'}-500 focus:border-transparent text-sm"
                                           placeholder="0"
                                           oninput="formatInteger(this); calculateItemTotal(${item.id})"
                                           onblur="validateInteger(this)">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan Realisasi</label>
                                    <input type="text" 
                                           name="item_realisasi[${item.id}][harga_satuan]" 
                                           value="${Math.round(item.harga_satuan).toLocaleString('id-ID').replace(/,/g, '.')}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-${divisi === 'peternakan' ? 'blue' : 'green'}-500 focus:border-transparent text-sm"
                                           placeholder="0"
                                           oninput="formatCurrency(this); calculateItemTotal(${item.id})"
                                           onblur="validateCurrency(this)">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Realisasi</label>
                                    <input type="text" 
                                           name="item_realisasi[${item.id}][keterangan]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-${divisi === 'peternakan' ? 'blue' : 'green'}-500 focus:border-transparent text-sm"
                                           placeholder="Keterangan realisasi">
                                </div>
                            </div>
                            <div class="mt-3 text-sm font-medium text-gray-700">
                                Total Realisasi: <span id="total-${item.id}" class="text-${divisi === 'peternakan' ? 'blue' : 'green'}-600">Rp ${item.total_harga.toLocaleString('id-ID').replace(/,/g, '.')}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            section.querySelector('.bg-' + (divisi === 'peternakan' ? 'blue' : 'green') + '-50').innerHTML = html;
        } else {
            section.querySelector('.bg-' + (divisi === 'peternakan' ? 'blue' : 'green') + '-50').innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada pengajuan dana untuk periode ini.</p>';
        }
    }
}

function clearPengajuanSections() {
    const sections = ['peternakan-section', 'perkebunan-section'];
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) {
            const bgClass = sectionId === 'peternakan-section' ? 'blue' : 'green';
            section.querySelector('.bg-' + bgClass + '-50').innerHTML = '<p class="text-gray-500 text-center py-4">Pilih divisi, bulan, dan tahun untuk melihat pengajuan dana.</p>';
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateFormVisibility();
    
    // Add event listeners to trigger functions when dropdowns change
    document.getElementById('divisi').addEventListener('change', function() {
        updateFormVisibility();
        showPendapatanSection();
    });
    document.getElementById('minggu').addEventListener('change', updateFormVisibility);
    document.getElementById('bulan').addEventListener('change', function() {
        showPendapatanSection();
        filterPengajuanDana();
    });
    document.getElementById('tahun').addEventListener('change', function() {
        showPendapatanSection();
        filterPengajuanDana();
    });
});

// New functions for edit features
let newItemIndex = 0;

// Function to toggle edit fields for selected items
function toggleItemEdit(itemId, isChecked) {
    const editFields = document.getElementById(`edit-fields-${itemId}`);
    if (editFields) {
        if (isChecked) {
            editFields.classList.remove('hidden');
        } else {
            editFields.classList.add('hidden');
        }
    }
    updateBalanceCalculation();
}

// Function to format integer (for jumlah - no decimals)
function formatInteger(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toString();
    }
    input.value = value;
}

// Function to format number (for jumlah with decimals - kept for compatibility)
function formatNumber(input) {
    let value = input.value.replace(/[^\d,]/g, '');
    if (value.includes(',')) {
        let parts = value.split(',');
        if (parts.length > 2) {
            value = parts[0] + ',' + parts.slice(1).join('');
        }
        if (parts[1] && parts[1].length > 2) {
            value = parts[0] + ',' + parts[1].substring(0, 2);
        }
    }
    input.value = value;
}

// Function to format currency (for harga)
function formatCurrency(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        // Use parseFloat to handle large numbers correctly
        value = parseFloat(value).toLocaleString('id-ID').replace(/,/g, '.');
    }
    input.value = value;
}

// Function to validate integer (for jumlah - no decimals)
function validateInteger(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value === '') {
        input.value = '0';
    } else {
        input.value = parseInt(value).toString();
    }
}

// Function to validate number (for jumlah with decimals - kept for compatibility)
function validateNumber(input) {
    let value = input.value.replace(/[^\d,]/g, '');
    if (value === '' || value === ',') {
        input.value = '0,00';
    } else if (!value.includes(',')) {
        input.value = value + ',00';
    } else if (value.endsWith(',')) {
        input.value = value + '00';
    } else if (value.split(',')[1] && value.split(',')[1].length === 1) {
        input.value = value + '0';
    }
}

// Function to validate currency
function validateCurrency(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value === '') {
        input.value = '0';
    } else {
        // Use parseFloat to handle large numbers correctly
        input.value = parseFloat(value).toLocaleString('id-ID').replace(/,/g, '.');
    }
}

// Function to get numeric value from formatted string
function getNumericValue(formattedValue) {
    if (formattedValue.includes(',') && formattedValue.split(',')[1]) {
        // Handle decimal numbers (jumlah with decimals)
        return parseFloat(formattedValue.replace(/\./g, '').replace(',', '.')) || 0;
    } else {
        // Handle integer (jumlah without decimals) and currency (harga)
        // Use parseFloat to handle large numbers correctly
        return parseFloat(formattedValue.replace(/\./g, '')) || 0;
    }
}

// Function to calculate total for individual item
function calculateItemTotal(itemId) {
    const jumlahInput = document.querySelector(`input[name="item_realisasi[${itemId}][jumlah]"]`);
    const hargaInput = document.querySelector(`input[name="item_realisasi[${itemId}][harga_satuan]"]`);
    const totalDiv = document.getElementById(`total-${itemId}`);
    const realisasiSpan = document.getElementById(`realisasi-${itemId}`);
    
    if (jumlahInput && hargaInput && totalDiv && realisasiSpan) {
        const jumlah = getNumericValue(jumlahInput.value);
        const harga = getNumericValue(hargaInput.value);
        const total = jumlah * harga;
        
        totalDiv.textContent = 'Rp ' + total.toLocaleString('id-ID').replace(/,/g, '.');
        realisasiSpan.textContent = 'Rp ' + total.toLocaleString('id-ID').replace(/,/g, '.');
    }
    updateBalanceCalculation();
}

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
                <input type="text" name="new_items[${newItemIndex}][jumlah]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="0" oninput="formatInteger(this); calculateNewItemTotal(this)" onblur="validateInteger(this)">
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
                <input type="text" name="new_items[${newItemIndex}][harga_satuan]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                    placeholder="0" oninput="formatCurrency(this); calculateNewItemTotal(this)" onblur="validateCurrency(this)">
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
    
    const jumlah = getNumericValue(jumlahInput.value);
    const harga = getNumericValue(hargaInput.value);
    const total = jumlah * harga;
    
    totalInput.value = total > 0 ? 'Rp ' + total.toLocaleString('id-ID').replace(/,/g, '.') : '';
    updateBalanceCalculation();
}

// Function to update balance calculation
function updateBalanceCalculation() {
    let totalPengajuan = 0;
    let totalRealisasi = 0;
    
    // Calculate from selected items with realisasi data
    const selectedItems = document.querySelectorAll('input[name="selected_items[]"]:checked');
    selectedItems.forEach(checkbox => {
        const itemId = checkbox.value;
        const jumlahInput = document.querySelector(`input[name="item_realisasi[${itemId}][jumlah]"]`);
        const hargaInput = document.querySelector(`input[name="item_realisasi[${itemId}][harga_satuan]"]`);
        
        if (jumlahInput && hargaInput) {
            const jumlah = getNumericValue(jumlahInput.value);
            const harga = getNumericValue(hargaInput.value);
            totalRealisasi += (jumlah * harga);
        }
    });
    
    // Calculate from new items
    const newItems = document.querySelectorAll('#new-items-container .bg-white');
    newItems.forEach(item => {
        const jumlahInput = item.querySelector('input[name*="[jumlah]"]');
        const hargaInput = item.querySelector('input[name*="[harga_satuan]"]');
        
        if (jumlahInput && hargaInput) {
            const jumlah = getNumericValue(jumlahInput.value);
            const harga = getNumericValue(hargaInput.value);
            totalRealisasi += (jumlah * harga);
        }
    });
    
    // Calculate saldo (for now, we assume pengajuan = realisasi initially)
    const saldo = totalPengajuan - totalRealisasi;
    const sisaSaldo = saldo > 0 ? saldo : 0;
    const saldoMinus = saldo < 0 ? Math.abs(saldo) : 0;
    
    // Update display
    document.getElementById('totalPengajuan').textContent = 'Rp ' + totalPengajuan.toLocaleString('id-ID').replace(/,/g, '.');
    document.getElementById('totalRealisasi').textContent = 'Rp ' + totalRealisasi.toLocaleString('id-ID').replace(/,/g, '.');
    document.getElementById('sisaSaldo').textContent = 'Rp ' + sisaSaldo.toLocaleString('id-ID').replace(/,/g, '.');
    document.getElementById('saldoMinus').textContent = 'Rp ' + saldoMinus.toLocaleString('id-ID').replace(/,/g, '.');
}
</script>
@endsection 