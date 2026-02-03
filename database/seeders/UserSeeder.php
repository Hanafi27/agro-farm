<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Owner
        $owner = User::create([
            'name' => 'Owner Agro Farm',
            'email' => 'owner@agrofarm.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        Pegawai::create([
            'user_id' => $owner->id,
            'nama' => 'Owner Agro Farm',
            'divisi' => 'Owner',
            'kontak' => '081234567890',
            'alamat' => 'Jl. Ciwidey No. 1',
            'gaji_pokok' => 10000000,
        ]);

        // Create Admin
        $admin = User::create([
            'name' => 'Admin Agro Farm',
            'email' => 'admin@agrofarm.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Pegawai::create([
            'user_id' => $admin->id,
            'nama' => 'Admin Agro Farm',
            'divisi' => 'Administrator',
            'kontak' => '081234567891',
            'alamat' => 'Jl. Ciwidey No. 2',
            'gaji_pokok' => 5000000,
        ]);

        // Create Keuangan
        $keuangan = User::create([
            'name' => 'Keuangan Agro Farm',
            'email' => 'keuangan@agrofarm.com',
            'password' => Hash::make('password'),
            'role' => 'keuangan',
        ]);

        Pegawai::create([
            'user_id' => $keuangan->id,
            'nama' => 'Keuangan Agro Farm',
            'divisi' => 'Keuangan',
            'kontak' => '081234567892',
            'alamat' => 'Jl. Ciwidey No. 3',
            'gaji_pokok' => 4000000,
        ]);

        // Create Pekerja Perkebunan
        $this->createPekerjaPerkebunan();
        
        // Create Pekerja Pertanian
        $this->createPekerjaPertanian();
    }

    private function createPekerjaPerkebunan()
    {
        $pekerjaPerkebunan = [
            'Sidik',
            'Irgi', 
            'Juhana',
            'Ari',
            'Asep',
            'Deni',
            'Cece',
            'Dude'
        ];

        foreach ($pekerjaPerkebunan as $index => $nama) {
            $user = User::create([
                'name' => $nama,
                'email' => strtolower($nama) . '@agrofarm.com',
                'password' => Hash::make('password'),
                'role' => 'pegawai', // Role pegawai untuk pekerja
            ]);

            Pegawai::create([
                'user_id' => $user->id,
                'nama' => $nama,
                'divisi' => 'Perkebunan',
                'kontak' => '08' . str_pad(rand(100000000, 999999999), 9, '0'),
                'alamat' => 'Jl. Ciwidey No. ' . ($index + 10),
                'gaji_pokok' => 3000000,
            ]);
        }
    }

    private function createPekerjaPertanian()
    {
        $pekerjaPertanian = [
            'Toto',
            'Abey',
            'Ahim', 
            'Nanang',
            'Omon',
            'Irfan',
            'Cepi',
            'Nuning',
            'Alit',
            'Odas'
        ];

        foreach ($pekerjaPertanian as $index => $nama) {
            $user = User::create([
                'name' => $nama,
                'email' => strtolower($nama) . '@agrofarm.com',
                'password' => Hash::make('password'),
                'role' => 'pegawai', // Role pegawai untuk pekerja
            ]);

            Pegawai::create([
                'user_id' => $user->id,
                'nama' => $nama,
                'divisi' => 'Pertanian',
                'kontak' => '08' . str_pad(rand(100000000, 999999999), 9, '0'),
                'alamat' => 'Jl. Ciwidey No. ' . ($index + 20),
                'gaji_pokok' => 2800000,
            ]);
        }
    }
}
