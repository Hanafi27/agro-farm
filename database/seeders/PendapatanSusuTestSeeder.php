<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PendapatanSusu;
use Carbon\Carbon;

class PendapatanSusuTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data pendapatan untuk peternakan bulan Juli 2025
        PendapatanSusu::create([
            'tanggal' => Carbon::create(2025, 7, 5),
            'kategori' => 'peternakan',
            'jenis_produk' => 'susu_sapi',
            'jumlah_liter' => 50.5,
            'satuan' => 'liter',
            'harga_per_liter' => 15000,
            'total_pendapatan' => 757500,
            'keterangan' => 'Pendapatan susu sapi minggu 1'
        ]);

        PendapatanSusu::create([
            'tanggal' => Carbon::create(2025, 7, 12),
            'kategori' => 'peternakan',
            'jenis_produk' => 'susu_sapi',
            'jumlah_liter' => 48.2,
            'satuan' => 'liter',
            'harga_per_liter' => 15000,
            'total_pendapatan' => 723000,
            'keterangan' => 'Pendapatan susu sapi minggu 2'
        ]);

        PendapatanSusu::create([
            'tanggal' => Carbon::create(2025, 7, 19),
            'kategori' => 'peternakan',
            'jenis_produk' => 'susu_kambing',
            'jumlah_liter' => 25.0,
            'satuan' => 'liter',
            'harga_per_liter' => 25000,
            'total_pendapatan' => 625000,
            'keterangan' => 'Pendapatan susu kambing minggu 3'
        ]);

        PendapatanSusu::create([
            'tanggal' => Carbon::create(2025, 7, 26),
            'kategori' => 'peternakan',
            'jenis_produk' => 'susu_kambing',
            'jumlah_liter' => 28.5,
            'satuan' => 'liter',
            'harga_per_liter' => 25000,
            'total_pendapatan' => 712500,
            'keterangan' => 'Pendapatan susu kambing minggu 4'
        ]);

        // Data pendapatan untuk perkebunan bulan Juli 2025
        PendapatanSusu::create([
            'tanggal' => Carbon::create(2025, 7, 8),
            'kategori' => 'perkebunan',
            'jenis_produk' => 'teh',
            'jumlah_liter' => 100.0,
            'satuan' => 'kg',
            'harga_per_liter' => 50000,
            'total_pendapatan' => 5000000,
            'keterangan' => 'Pendapatan teh minggu 1'
        ]);

        PendapatanSusu::create([
            'tanggal' => Carbon::create(2025, 7, 15),
            'kategori' => 'perkebunan',
            'jenis_produk' => 'teh',
            'jumlah_liter' => 95.5,
            'satuan' => 'kg',
            'harga_per_liter' => 50000,
            'total_pendapatan' => 4775000,
            'keterangan' => 'Pendapatan teh minggu 2'
        ]);

        $this->command->info('Data pendapatan test untuk Juli 2025 berhasil dibuat!');
    }
}
