@extends('layouts.app')

@section('title', 'Edit Pegawai')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Pegawai</h1>
            <p class="text-gray-600 mt-2">Update data pegawai</p>
        </div>
        <a href="{{ route('admin.pegawai.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Edit Pegawai</h2>
        </div>
        
        <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- User Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Pegawai</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $pegawai->nama) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama') border-red-500 @enderror"
                           placeholder="Masukkan nama pegawai">
                    @error('nama')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $pegawai->user->email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="Masukkan email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="divisi" class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                    <select name="divisi" id="divisi" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('divisi') border-red-500 @enderror">
                        <option value="">Pilih Divisi</option>
                        <option value="Perkebunan" {{ old('divisi', $pegawai->divisi) == 'Perkebunan' ? 'selected' : '' }}>Perkebunan</option>
                        <option value="Pertanian" {{ old('divisi', $pegawai->divisi) == 'Pertanian' ? 'selected' : '' }}>Pertanian</option>
                    </select>
                    @error('divisi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="kontak" class="block text-sm font-medium text-gray-700 mb-2">Kontak</label>
                    <input type="text" name="kontak" id="kontak" value="{{ old('kontak', $pegawai->kontak) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('kontak') border-red-500 @enderror"
                           placeholder="Nomor telepon atau WhatsApp">
                    @error('kontak')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="gaji_pokok" class="block text-sm font-medium text-gray-700 mb-2">Gaji Pokok</label>
                    <input type="number" name="gaji_pokok" id="gaji_pokok" value="{{ old('gaji_pokok', $pegawai->gaji_pokok) }}" readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                           placeholder="Gaji pokok per bulan (hanya bisa diubah di database)">
                    <p class="text-xs text-gray-500 mt-1">Gaji pokok hanya dapat diubah melalui database</p>
                </div>
                
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('alamat') border-red-500 @enderror"
                              placeholder="Alamat lengkap pegawai">{{ old('alamat', $pegawai->alamat) }}</textarea>
                    @error('alamat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.pegawai.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    Batal
                </a>
                <button type="submit" class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-blue-600 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Update Pegawai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 