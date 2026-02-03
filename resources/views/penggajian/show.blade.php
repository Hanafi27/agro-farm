@extends('layouts.app')

@section('title', 'Detail Penggajian - Ciwidey Agro Farm')

@section('page-title', 'Detail Penggajian')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Penggajian</h2>
                <p class="text-gray-600 mt-1">Informasi lengkap penggajian pegawai</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.penggajian.export-slip', $penggajian->id) }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export Slip
                </a>
                <a href="{{ route('admin.penggajian.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Employee Information Card -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Informasi Pegawai</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-r from-agro-green to-agro-blue rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-xl">{{ substr($penggajian->pegawai->nama, 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800">{{ $penggajian->pegawai->nama }}</h4>
                            <p class="text-gray-600">Pekerja: {{ $penggajian->pegawai->divisi }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p><strong>Email:</strong> {{ $penggajian->pegawai->user->email }}</p>
                        <p><strong>Kontak:</strong> {{ $penggajian->pegawai->kontak }}</p>
                        <p><strong>Alamat:</strong> {{ $penggajian->pegawai->alamat }}</p>
                    </div>
                </div>
                <div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-semibold text-gray-800 mb-3">Periode Gaji</h5>
                        <div class="space-y-2">
                            @if($penggajian->tipe_periode === 'harian')
                                <p><strong>Tipe:</strong> Harian</p>
                                <p><strong>Tanggal:</strong> {{ optional($penggajian->tanggal)->format('d/m/Y') ?? '-' }}</p>
                            @else
                                <p><strong>Tipe:</strong> Bulanan</p>
                                <p><strong>Bulan:</strong> {{ \Carbon\Carbon::createFromDate($penggajian->tahun, $penggajian->bulan, 1)->translatedFormat('F Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Summary Card -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Ringkasan Kehadiran</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $penggajian->total_hadir }}</div>
                    <div class="text-sm font-medium text-green-800">Hadir</div>
                    <div class="text-xs text-green-600">Hari</div>
                </div>
                <div class="text-center p-6 bg-yellow-50 rounded-lg">
                    <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $penggajian->total_izin }}</div>
                    <div class="text-sm font-medium text-yellow-800">Izin</div>
                    <div class="text-xs text-yellow-600">Hari</div>
                </div>
                <div class="text-center p-6 bg-red-50 rounded-lg">
                    <div class="text-3xl font-bold text-red-600 mb-2">{{ $penggajian->total_alfa }}</div>
                    <div class="text-sm font-medium text-red-800">Alfa</div>
                    <div class="text-xs text-red-600">Hari</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Details Card -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Rincian Gaji</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-700">Gaji Pokok per Bulan</span>
                    <span class="font-semibold">Rp {{ number_format($penggajian->gaji_per_bulan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-700">Gaji per Minggu</span>
                    <span class="font-semibold">Rp {{ number_format($penggajian->gaji_per_minggu, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-700">Potongan (Izin + Alfa)</span>
                    <span class="font-semibold text-red-600">- Rp {{ number_format($penggajian->potongan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-4 bg-green-50 rounded-lg px-4">
                    <span class="text-lg font-semibold text-gray-800">TOTAL GAJI</span>
                    <span class="text-2xl font-bold text-green-600">Rp {{ number_format($penggajian->total_gaji, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculation Details Card -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detail Perhitungan</h3>
        </div>
        <div class="p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 mb-3">Formula Perhitungan</h4>
                <div class="space-y-2 text-sm text-blue-700">
                    <p>• Gaji per hari = Gaji per bulan ÷ 30 hari = Rp {{ number_format($penggajian->gaji_per_bulan / 30, 0, ',', '.') }}</p>
                    <p>• Potongan = (Total Izin + Total Alfa) × Gaji per hari</p>
                    <p>• Total Gaji = Gaji per bulan - Potongan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Aksi</h3>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.penggajian.edit', $penggajian->id) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Penggajian
                    </a>
                
                    <form action="{{ route('admin.penggajian.destroy', $penggajian->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors flex items-center" onclick="return confirm('Yakin ingin menghapus penggajian ini?')">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Penggajian
                        </button>
                    </form>
            </div>
        </div>
    </div>
</div>


@endsection 