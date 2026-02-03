@extends('layouts.app')

@section('title', 'Dashboard - Ciwidey Agro Farm')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-agro-green to-agro-blue rounded-lg shadow-md p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->name }}!</h1>
                <p class="text-lg opacity-90">Sistem Informasi Ciwidey Agro Farm</p>
                <p class="text-sm opacity-75 mt-1">Kelola data pegawai, absensi, penggajian, dan keuangan dengan mudah</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Pegawai -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pegawai</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pegawai'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Total Pengajuan Dana -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pengajuan Dana</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pengajuan_dana'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        @if($user->role === 'admin')
        <!-- Total Absensi -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Absensi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_absensi'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-milk-bottle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pendapatan_susu'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Total Penggajian -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Penggajian</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_penggajian'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Pengajuan -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Pengajuan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_pengajuan'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        @endif

        @if(in_array($user->role, ['owner', 'keuangan']))
        <!-- Pending Pengajuan -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Pengajuan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_pengajuan'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        @if($user->role === 'keuangan')
        <!-- Laporan Realisasi -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Realisasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_laporan_realisasi'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($user->role === 'admin')
            <a href="{{ route('admin.pegawai.create') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                <i class="fas fa-user-plus mr-3"></i>
                <span>Tambah Pegawai</span>
            </a>
            <a href="{{ route('admin.absensi.create') }}" class="flex items-center p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all">
                <i class="fas fa-clock mr-3"></i>
                <span>Input Absensi</span>
            </a>
            <a href="{{ route('admin.pendapatan-susu.create') }}" class="flex items-center p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all">
                <i class="fas fa-milk-bottle mr-3"></i>
                <span>Input Pendapatan</span>
            </a>
            <a href="{{ route('admin.penggajian.create') }}" class="flex items-center p-4 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition-all">
                <i class="fas fa-money-bill-wave mr-3"></i>
                <span>Proses Penggajian</span>
            </a>
            <a href="{{ route('admin.pengajuan-dana.create') }}" class="flex items-center p-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                <i class="fas fa-hand-holding-usd mr-3"></i>
                <span>Ajukan Dana</span>
            </a>
            
            @endif

            @if($user->role === 'owner')
            <a href="{{ route('owner.pengajuan-dana.index') }}" class="flex items-center p-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                <i class="fas fa-hand-holding-usd mr-3"></i>
                <span>Review Pengajuan</span>
            </a>
            <a href="{{ route('owner.laporan-realisasi.index') }}" class="flex items-center p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Laporan Realisasi</span>
            </a>
            
            @endif

            @if($user->role === 'keuangan')
            <a href="{{ route('keuangan.pengajuan-dana.index') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                <i class="fas fa-hand-holding-usd mr-3"></i>
                <span>Realiasi Dana</span>
            </a>
            <a href="{{ route('keuangan.laporan-realisasi.index') }}" class="flex items-center p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Lihat Laporan</span>
            </a>
            
            @endif
        </div>
    </div>
</div>
@endsection
