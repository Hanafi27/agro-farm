@extends('layouts.app')

@section('title', 'Tambah Penggajian - Ciwidey Agro Farm')

@section('page-title', 'Tambah Penggajian')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tambah Penggajian Baru</h2>
                <p class="text-gray-600 mt-1">Buat penggajian dengan perhitungan otomatis berdasarkan absensi</p>
            </div>
            <a href="{{ route('admin.penggajian.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Data Penggajian</h3>
        </div>
        
        <form action="{{ route('admin.penggajian.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Pegawai
                    </label>
                    <select name="pegawai_id" id="pegawai_id" required onchange="loadAbsensiData(); loadGajiPokok()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="">Pilih Pegawai</option>
                        @foreach($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}" data-gaji-pokok="{{ $pegawai->gaji_pokok ?? 5000000 }}">
                            {{ $pegawai->nama }} - {{ $pegawai->divisi }}
                        </option>
                        @endforeach
                    </select>
                    @error('pegawai_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipe_periode" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2"></i>Tipe Periode
                    </label>
                    <select name="tipe_periode" id="tipe_periode" required onchange="togglePeriode()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="bulanan" selected>Bulanan</option>
                        <option value="harian">Harian</option>
                    </select>
                    @error('tipe_periode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="row-bulanan" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Bulan
                    </label>
                    <select name="bulan" id="bulan" onchange="loadAbsensiData()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="">Pilih Bulan</option>
                        <option value="1" {{ $bulanSekarang == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ $bulanSekarang == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ $bulanSekarang == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ $bulanSekarang == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ $bulanSekarang == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ $bulanSekarang == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ $bulanSekarang == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ $bulanSekarang == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ $bulanSekarang == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $bulanSekarang == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ $bulanSekarang == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $bulanSekarang == 12 ? 'selected' : '' }}>Desember</option>
                    </select>
                    @error('bulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-year mr-2"></i>Tahun
                    </label>
                    <input type="number" name="tahun" id="tahun" value="{{ $tahunSekarang }}" onchange="loadAbsensiData()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('tahun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="row-harian" class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-day mr-2"></i>Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="gaji_per_bulan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave mr-2"></i>Gaji per Bulan (Rp)
                    </label>
                    <input type="text" name="gaji_per_bulan_display" id="gaji_per_bulan_display" value="{{ old('gaji_per_bulan', number_format(5000000, 0, ',', '.')) }}" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                        placeholder="Masukkan gaji per bulan" oninput="formatCurrency(this)" onchange="calculateGajiMinggu()">
                    <input type="hidden" name="gaji_per_bulan" id="gaji_per_bulan" value="{{ old('gaji_per_bulan', 5000000) }}">
                    @error('gaji_per_bulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gaji_per_minggu" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill mr-2"></i>Gaji per Minggu
                    </label>
                    <input type="number" name="gaji_per_minggu" id="gaji_per_minggu" value="{{ old('gaji_per_minggu', 1250000) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                        placeholder="Masukkan gaji per minggu">
                    @error('gaji_per_minggu')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                    placeholder="Masukkan keterangan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Calculation Display -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Perhitungan Otomatis</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Hadir</label>
                        <div class="text-lg font-semibold text-green-600" id="display_total_hadir">0</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Izin</label>
                        <div class="text-lg font-semibold text-yellow-600" id="display_total_izin">0</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Alfa</label>
                        <div class="text-lg font-semibold text-red-600" id="display_total_alfa">0</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Potongan</label>
                        <div class="text-lg font-semibold text-red-600" id="display_potongan">Rp 0</div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium text-gray-700">Total Gaji:</span>
                        <span class="text-2xl font-bold text-green-600" id="display_total_gaji">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Informasi Perhitungan</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            • Gaji per hari = Gaji per bulan ÷ 30 hari<br>
                            • Potongan = (Total Izin + Total Alfa) × Gaji per hari<br>
                            • Total Gaji = Gaji per bulan - Potongan<br>
                            • Data absensi diambil otomatis dari sistem
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.penggajian.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Penggajian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function formatCurrency(input) {
    let value = input.value.replace(/\D/g, '');
    let formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    input.value = formatted;
    document.getElementById('gaji_per_bulan').value = value;
}

function loadGajiPokok() {
    const pegawaiSelect = document.getElementById('pegawai_id');
    const selectedOption = pegawaiSelect.options[pegawaiSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.gajiPokok) {
        const gajiPokok = selectedOption.dataset.gajiPokok;
        const formatted = parseInt(gajiPokok).toLocaleString('id-ID');
        
        document.getElementById('gaji_per_bulan_display').value = formatted;
        document.getElementById('gaji_per_bulan').value = gajiPokok;
        
        calculateGajiMinggu();
    }
}

function togglePeriode() {
    const tipe = document.getElementById('tipe_periode').value;
    const rowBulanan = document.getElementById('row-bulanan');
    const rowHarian = document.getElementById('row-harian');

    if (tipe === 'harian') {
        rowBulanan.classList.add('hidden');
        rowHarian.classList.remove('hidden');
    } else {
        rowBulanan.classList.remove('hidden');
        rowHarian.classList.add('hidden');
    }
}

function loadAbsensiData() {
    const pegawaiId = document.getElementById('pegawai_id').value;
    const bulan = document.getElementById('bulan').value;
    const tahun = document.getElementById('tahun').value;
    
    if (pegawaiId && bulan && tahun) {
        fetch(`/admin/api/absensi-count/${pegawaiId}/${bulan}/${tahun}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('display_total_hadir').textContent = data.total_hadir || 0;
                document.getElementById('display_total_izin').textContent = data.total_izin || 0;
                document.getElementById('display_total_alfa').textContent = data.total_alfa || 0;
                calculatePotongan();
            })
            .catch(error => {
                console.error('Error loading absensi data:', error);
            });
    }
}

function calculateGajiMinggu() {
    const gajiPerBulan = parseFloat(document.getElementById('gaji_per_bulan').value) || 0;
    const gajiPerMinggu = gajiPerBulan / 4; // Asumsi 4 minggu per bulan
    document.getElementById('gaji_per_minggu').value = Math.round(gajiPerMinggu);
    calculatePotongan();
}

function calculatePotongan() {
    const gajiPerBulan = parseFloat(document.getElementById('gaji_per_bulan').value) || 0;
    const totalIzin = parseInt(document.getElementById('display_total_izin').textContent) || 0;
    const totalAlfa = parseInt(document.getElementById('display_total_alfa').textContent) || 0;
    
    const gajiPerHari = gajiPerBulan / 30;
    const potongan = (totalIzin + totalAlfa) * gajiPerHari;
    const totalGaji = gajiPerBulan - potongan;
    
    document.getElementById('display_potongan').textContent = 'Rp ' + potongan.toLocaleString('id-ID');
    document.getElementById('display_total_gaji').textContent = 'Rp ' + totalGaji.toLocaleString('id-ID');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateGajiMinggu();
    togglePeriode();
});
</script>
@endsection 