@extends('layouts.app')

@section('title', 'Edit Penggajian - Ciwidey Agro Farm')

@section('page-title', 'Edit Penggajian')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Edit Penggajian</h2>
                <p class="text-gray-600 mt-1">Edit data penggajian pegawai</p>
            </div>
            <a href="{{ route('admin.penggajian.show', $penggajian->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
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
        
        <form action="{{ route('admin.penggajian.update', $penggajian->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Pegawai
                    </label>
                    <select name="pegawai_id" id="pegawai_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        @foreach($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}" {{ $penggajian->pegawai_id == $pegawai->id ? 'selected' : '' }}>
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
                        <option value="bulanan" {{ $penggajian->tipe_periode === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="harian" {{ $penggajian->tipe_periode === 'harian' ? 'selected' : '' }}>Harian</option>
                    </select>
                    @error('tipe_periode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="row-bulanan" class="grid grid-cols-1 md:grid-cols-2 gap-6 {{ $penggajian->tipe_periode === 'harian' ? 'hidden' : '' }}">
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Bulan
                    </label>
                    <select name="bulan" id="bulan"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                        <option value="1" {{ $penggajian->bulan == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ $penggajian->bulan == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ $penggajian->bulan == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ $penggajian->bulan == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ $penggajian->bulan == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ $penggajian->bulan == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ $penggajian->bulan == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ $penggajian->bulan == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ $penggajian->bulan == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $penggajian->bulan == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ $penggajian->bulan == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $penggajian->bulan == 12 ? 'selected' : '' }}>Desember</option>
                    </select>
                    @error('bulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-year mr-2"></i>Tahun
                    </label>
                    <input type="number" name="tahun" id="tahun" value="{{ $penggajian->tahun }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('tahun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="row-harian" class="grid grid-cols-1 md:grid-cols-2 gap-6 {{ $penggajian->tipe_periode === 'harian' ? '' : 'hidden' }}">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-day mr-2"></i>Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ $penggajian->tanggal ? $penggajian->tanggal->format('Y-m-d') : '' }}"
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
                    <input type="text" name="gaji_per_bulan_display" id="gaji_per_bulan_display" value="{{ number_format($penggajian->gaji_per_bulan, 0, ',', '.') }}" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                        placeholder="Masukkan gaji per bulan" oninput="formatCurrency(this)">
                    <input type="hidden" name="gaji_per_bulan" id="gaji_per_bulan" value="{{ $penggajian->gaji_per_bulan }}">
                    @error('gaji_per_bulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gaji_per_minggu" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill mr-2"></i>Gaji per Minggu
                    </label>
                    <input type="number" name="gaji_per_minggu" id="gaji_per_minggu" value="{{ $penggajian->gaji_per_minggu }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                        placeholder="Masukkan gaji per minggu">
                    @error('gaji_per_minggu')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="total_hadir" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check-circle mr-2"></i>Total Hadir
                    </label>
                    <input type="number" name="total_hadir" id="total_hadir" value="{{ $penggajian->total_hadir }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('total_hadir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_izin" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2"></i>Total Izin
                    </label>
                    <input type="number" name="total_izin" id="total_izin" value="{{ $penggajian->total_izin }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('total_izin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="total_alfa" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-times-circle mr-2"></i>Total Alfa
                    </label>
                    <input type="number" name="total_alfa" id="total_alfa" value="{{ $penggajian->total_alfa }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('total_alfa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="potongan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-minus-circle mr-2"></i>Potongan
                    </label>
                    <input type="number" name="potongan" id="potongan" value="{{ $penggajian->potongan }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                    @error('potongan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="total_gaji" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill-wave mr-2"></i>Total Gaji
                </label>
                <input type="number" name="total_gaji" id="total_gaji" value="{{ $penggajian->total_gaji }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors">
                @error('total_gaji')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agro-green focus:border-transparent transition-colors"
                    placeholder="Masukkan keterangan (opsional)">{{ $penggajian->keterangan }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Informasi Edit</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            • Penggajian harian menggunakan tanggal dan 1 hari absen<br>
                            • Penggajian bulanan menggunakan bulan/tahun dan rekap absensi<br>
                            • Pastikan data yang diinput sudah benar
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.penggajian.show', $penggajian->id) }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-agro-green to-agro-blue text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Update Penggajian
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

document.addEventListener('DOMContentLoaded', togglePeriode);
</script>
@endsection 