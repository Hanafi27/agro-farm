@extends('layouts.app')

@section('title', 'Detail Absensi - Ciwidey Agro Farm')

@section('page-title', 'Detail Absensi')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Absensi</h2>
                <p class="text-gray-600 mt-1">Informasi lengkap absensi pegawai</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.absensi.edit', $absensi->id) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Informasi Absensi</h3>
        </div>
        <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Employee Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pegawai</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->pegawai->nama }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Divisi</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->pegawai->divisi }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->pegawai->user->email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kontak</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->pegawai->kontak }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Absensi</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->tanggal ? $absensi->tanggal->format('d M Y') : '-' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jam Masuk</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->jam_masuk ? $absensi->jam_masuk->format('H:i') : '-' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jam Keluar</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->jam_keluar ? $absensi->jam_keluar->format('H:i') : '-' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($absensi->status === 'hadir') bg-green-100 text-green-800
                                        @elseif($absensi->status === 'izin') bg-yellow-100 text-yellow-800
                                        @elseif($absensi->status === 'alfa') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($absensi->status) }}
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->keterangan ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Working Hours Calculation -->
                    @if($absensi->jam_masuk && $absensi->jam_keluar)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Perhitungan Jam Kerja</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                @php
                                    $jam_masuk = \Carbon\Carbon::parse($absensi->jam_masuk);
                                    $jam_keluar = \Carbon\Carbon::parse($absensi->jam_keluar);
                                    $durasi = $jam_keluar->diffInHours($jam_masuk);
                                    $menit = $jam_keluar->diffInMinutes($jam_masuk) % 60;
                                @endphp
                                <div class="text-sm text-gray-700">
                                    <p><strong>Durasi Kerja:</strong> {{ $durasi }} jam {{ $menit }} menit</p>
                                    <p><strong>Jam Kerja Standar:</strong> 8 jam</p>
                                    @if($durasi > 8)
                                        <p class="text-green-600"><strong>Lembur:</strong> {{ $durasi - 8 }} jam {{ $menit }} menit</p>
                                    @elseif($durasi < 8)
                                        <p class="text-red-600"><strong>Kurang:</strong> {{ 8 - $durasi }} jam {{ 60 - $menit }} menit</p>
                                    @else
                                        <p class="text-blue-600"><strong>Jam Kerja:</strong> Tepat waktu</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 