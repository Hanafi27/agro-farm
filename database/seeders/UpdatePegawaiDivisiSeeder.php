<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;

class UpdatePegawaiDivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing data to use divisi instead of jabatan
        DB::table('pegawais')->where('divisi', 'Pekerja Kandang')->update(['divisi' => 'Perkebunan']);
        DB::table('pegawais')->where('divisi', 'Pekerja Kebun')->update(['divisi' => 'Pertanian']);
        
        // Update role for workers to be 'pegawai' instead of 'admin'
        DB::table('users')
            ->whereIn('email', [
                'sidik@agrofarm.com', 'irgi@agrofarm.com', 'juhana@agrofarm.com', 'ari@agrofarm.com',
                'asep@agrofarm.com', 'deni@agrofarm.com', 'cece@agrofarm.com', 'dude@agrofarm.com',
                'toto@agrofarm.com', 'abey@agrofarm.com', 'ahim@agrofarm.com', 'nanang@agrofarm.com',
                'omon@agrofarm.com', 'irfan@agrofarm.com', 'cepi@agrofarm.com', 'nuning@agrofarm.com',
                'alit@agrofarm.com', 'odas@agrofarm.com'
            ])
            ->update(['role' => 'pegawai']);
    }
}
