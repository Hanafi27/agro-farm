@extends('layouts.app')

@section('title', 'Detail Pendapatan - Ciwidey Agro Farm')

@section('page-title', 'Detail Pendapatan')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Pendapatan</h2>
                <p class="text-gray-600 mt-1">Informasi lengkap data pendapatan</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.pendapatan-susu.edit', $pendapatanSusu->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.pendapatan-susu.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Data Pendapatan</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-calendar text-blue-500 mr-3"></i>
                        <h4 class="font-medium text-gray-800">Tanggal</h4>
                    </div>
                    <p class="text-gray-600">{{ $pendapatanSusu->tanggal->format('d F Y') }}</p>
                </div>

                <!-- Kategori -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-tags text-green-500 mr-3"></i>
                        <h4 class="font-medium text-gray-800">Kategori</h4>
                    </div>
                    <p class="text-gray-600 capitalize">{{ $pendapatanSusu->kategori }}</p>
                </div>

                <!-- Jenis Produk -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-leaf text-green-500 mr-3"></i>
                        <h4 class="font-medium text-gray-800">Jenis Produk</h4>
                    </div>
                    <p class="text-gray-600">
                        @if($pendapatanSusu->jenis_produk == 'teh')
                            Teh
                        @elseif($pendapatanSusu->jenis_produk == 'susu_kambing')
                            Susu Kambing
                        @elseif($pendapatanSusu->jenis_produk == 'susu_sapi')
                            Susu Sapi
                        @else
                            {{ $pendapatanSusu->jenis_produk }}
                        @endif
                    </p>
                </div>

                <!-- Jumlah Produk -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-balance-scale text-purple-500 mr-3"></i>
                        <h4 class="font-medium text-gray-800">Jumlah Produk</h4>
                    </div>
                    <p class="text-gray-600">{{ number_format($pendapatanSusu->jumlah_liter, 2) }} {{ $pendapatanSusu->satuan }}</p>
                </div>

                <!-- Harga per Satuan -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-money-bill-wave text-yellow-500 mr-3"></i>
                        <h4 class="font-medium text-gray-800">Harga per Satuan</h4>
                    </div>
                    <p class="text-gray-600">Rp {{ number_format($pendapatanSusu->harga_per_liter, 0, ',', '.') }}</p>
                </div>

                <!-- Total Pendapatan -->
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-coins text-green-500 mr-3"></i>
                        <h4 class="font-medium text-gray-800">Total Pendapatan</h4>
                    </div>
                    <p class="text-green-600 font-semibold text-lg">Rp {{ number_format($pendapatanSusu->total_pendapatan, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Keterangan -->
            @if($pendapatanSusu->keterangan)
            <div class="mt-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-sticky-note text-blue-500 mr-3"></i>
                        <h4 class="font-medium text-gray-800">Keterangan</h4>
                    </div>
                    <p class="text-gray-600">{{ $pendapatanSusu->keterangan }}</p>
                </div>
            </div>
            @endif

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Informasi Kategori</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            • <strong>Perkebunan:</strong> Produk Teh<br>
                            • <strong>Peternakan:</strong> Susu Kambing atau Susu Sapi<br>
                            • Data pendapatan dicatat berdasarkan kategori dan jenis produk
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 mt-6">
                <a href="{{ route('admin.pendapatan-susu.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar
                </a>
                <a href="{{ route('admin.pendapatan-susu.edit', $pendapatanSusu->id) }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Data
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
