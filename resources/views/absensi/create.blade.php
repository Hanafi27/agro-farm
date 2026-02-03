@extends('layouts.app')

@section('title', 'Input Absensi - Ciwidey Agro Farm')

@section('page-title', 'Input Absensi')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Input Absensi Harian</h2>
                <p class="text-gray-600 mt-1">Pilih tanggal dan checklist kehadiran pegawai</p>
            </div>
            <a href="{{ route('admin.absensi.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
            <div>
                <h4 class="text-sm font-medium text-blue-800">Petunjuk Input Absensi</h4>
                <p class="text-sm text-blue-700 mt-1">
                    • Pilih tanggal absensi terlebih dahulu (default hari ini)<br>
                    • Jam kerja default: 08:00 - 15:00 (dapat diubah)<br>
                    • Status "Izin" dan "Alfa" wajib diisi keterangan<br>
                    • Klik "Simpan Absensi" untuk menyimpan semua data sekaligus
                </p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Checklist Kehadiran Pegawai</h3>
        </div>
        
        <form action="{{ route('admin.absensi.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Absensi</label>
                <input type="date" name="tanggal" value="{{ $today ? $today->format('Y-m-d') : '' }}" 
                       class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent" required>
            </div>
            
            <!-- Quick Actions -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Aksi Cepat:</h4>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setAllStatus('hadir')" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                        <i class="fas fa-check mr-1"></i>Semua Hadir
                    </button>
                    <button type="button" onclick="setAllStatus('izin')" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">
                        <i class="fas fa-clock mr-1"></i>Semua Izin
                    </button>
                    <button type="button" onclick="setAllStatus('alpha')" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                        <i class="fas fa-times mr-1"></i>Semua Alfa
                    </button>
                    <button type="button" onclick="resetAll()" class="bg-gray-500 text-white px-3 py-1 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-refresh mr-1"></i>Reset Semua
                    </button>
                </div>
            </div>

            <!-- Absensi Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700">No</th>
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700">Nama Pegawai</th>
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700">Divisi</th>
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700">Jam Masuk</th>
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700">Jam Keluar</th>
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pegawaiList as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-200 px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="border border-gray-200 px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-agro-green to-agro-blue rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white font-bold text-xs">{{ substr($item['pegawai']->nama, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $item['pegawai']->nama }}</div>
                                        <div class="text-xs text-gray-500">{{ $item['pegawai']->divisi }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="border border-gray-200 px-4 py-3 text-sm text-gray-900">{{ $item['pegawai']->divisi }}</td>
                            <td class="border border-gray-200 px-4 py-3">
                                <select name="absensi_data[{{ $index }}][status]" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                                        onchange="toggleKeterangan({{ $index }})">
                                    <option value="">Pilih Status</option>
                                    <option value="hadir" {{ $item['absensi'] && $item['absensi']->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="izin" {{ $item['absensi'] && $item['absensi']->status == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="alpha" {{ $item['absensi'] && $item['absensi']->status == 'alpha' ? 'selected' : '' }}>Alfa</option>
                                    <option value="sakit" {{ $item['absensi'] && $item['absensi']->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                </select>
                                <input type="hidden" name="absensi_data[{{ $index }}][pegawai_id]" value="{{ $item['pegawai']->id }}">
                            </td>
                            <td class="border border-gray-200 px-4 py-3">
                                <input type="time" name="absensi_data[{{ $index }}][jam_masuk]" 
                                       value="{{ $item['absensi'] ? ($item['absensi']->jam_masuk ? $item['absensi']->jam_masuk->format('H:i') : '08:00') : '08:00' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm">
                            </td>
                            <td class="border border-gray-200 px-4 py-3">
                                <input type="time" name="absensi_data[{{ $index }}][jam_keluar]" 
                                       value="{{ $item['absensi'] ? ($item['absensi']->jam_keluar ? $item['absensi']->jam_keluar->format('H:i') : '15:00') : '15:00' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm">
                            </td>
                            <td class="border border-gray-200 px-4 py-3">
                                <textarea name="absensi_data[{{ $index }}][keterangan]" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent text-sm"
                                          placeholder="Keterangan (wajib untuk Izin/Alfa)">{{ $item['absensi'] ? $item['absensi']->keterangan : '' }}</textarea>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 mt-6">
                <a href="{{ route('admin.absensi.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Absensi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setAllStatus(status) {
    const statusSelects = document.querySelectorAll('select[name*="[status]"]');
    statusSelects.forEach((select, index) => {
        select.value = status;
        toggleKeterangan(index);
    });
}

function resetAll() {
    const statusSelects = document.querySelectorAll('select[name*="[status]"]');
    const keteranganTextareas = document.querySelectorAll('textarea[name*="[keterangan]"]');
    
    statusSelects.forEach(select => {
        select.value = '';
    });
    
    keteranganTextareas.forEach(textarea => {
        textarea.value = '';
        textarea.required = false;
        textarea.placeholder = 'Keterangan (opsional)';
    });
}

function toggleKeterangan(index) {
    const statusSelect = document.querySelector(`select[name="absensi_data[${index}][status]"]`);
    const keteranganTextarea = document.querySelector(`textarea[name="absensi_data[${index}][keterangan]"]`);
    
    if (statusSelect.value === 'izin' || statusSelect.value === 'alpha') {
        keteranganTextarea.required = true;
        keteranganTextarea.placeholder = 'Keterangan (wajib untuk ' + statusSelect.value.charAt(0).toUpperCase() + statusSelect.value.slice(1) + ')';
    } else {
        keteranganTextarea.required = false;
        keteranganTextarea.placeholder = 'Keterangan (opsional)';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('select[name*="[status]"]');
    statusSelects.forEach((select, index) => {
        toggleKeterangan(index);
    });
});
</script>
@endsection 