@extends('layouts.app')

@section('title', 'Detail Pegawai')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Pegawai</h1>
            <p class="text-gray-600 mt-2">Informasi lengkap pegawai</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.pegawai.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Pegawai Information -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Informasi Pegawai</h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Data Pribadi</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nama Lengkap</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $pegawai->nama }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email</label>
                            <p class="text-lg text-gray-900">{{ $pegawai->user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kontak</label>
                            <p class="text-lg text-gray-900">{{ $pegawai->kontak }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Alamat</label>
                            <p class="text-lg text-gray-900">{{ $pegawai->alamat }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Work Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Data Pekerjaan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Divisi</label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $pegawai->divisi == 'Perkebunan' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $pegawai->divisi }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Role</label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ ucfirst($pegawai->user->role) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Gaji Pokok</label>
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($pegawai->gaji_pokok, 0, ',', '.') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Bergabung</label>
                            <p class="text-lg text-gray-900">{{ $pegawai->created_at ? $pegawai->created_at->format('d F Y') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 