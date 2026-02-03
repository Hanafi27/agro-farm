<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LaporanRealisasi;
use App\Models\LaporanRealisasiItem;
use App\Models\User;
use Carbon\Carbon;

class LaporanRealisasiJuniJuliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user admin untuk sebagai pembuat laporan
        $admin = User::where('role', 'admin')->first();
        $owner = User::where('role', 'owner')->first();

        if (!$admin || !$owner) {
            $this->command->error('User admin atau owner tidak ditemukan!');
            return;
        }

        // Data untuk Juni 2025
        $this->createLaporanRealisasi(
            $admin->id,
            $owner->id,
            6, // Juni
            2025,
            'peternakan',
            1, // minggu 1
            'Laporan realisasi minggu pertama Juni 2025 untuk divisi peternakan',
            [
                // Pendapatan
                ['kategori' => 'pendapatan', 'nama_item' => 'Susu Sapi', 'jumlah' => 150, 'satuan' => 'liter', 'harga_satuan' => 8000, 'keterangan' => 'Hasil perahan sapi perah'],
                ['kategori' => 'pendapatan', 'nama_item' => 'Daging Sapi', 'jumlah' => 25, 'satuan' => 'kg', 'harga_satuan' => 120000, 'keterangan' => 'Penjualan daging sapi potong'],
                
                // Tenaga & Konsumsi
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Gaji Karyawan', 'jumlah' => 1, 'satuan' => 'bulan', 'harga_satuan' => 2500000, 'keterangan' => 'Gaji karyawan peternakan'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Pakan Sapi', 'jumlah' => 500, 'satuan' => 'kg', 'harga_satuan' => 5000, 'keterangan' => 'Pakan konsentrat sapi'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Rumput Hijau', 'jumlah' => 1000, 'satuan' => 'kg', 'harga_satuan' => 2000, 'keterangan' => 'Rumput hijau untuk pakan'],
                
                // Alat & Bahan
                ['kategori' => 'alat_bahan', 'nama_item' => 'Obat Vitamin', 'jumlah' => 50, 'satuan' => 'botol', 'harga_satuan' => 15000, 'keterangan' => 'Vitamin untuk sapi'],
                ['kategori' => 'alat_bahan', 'nama_item' => 'Alat Perah', 'jumlah' => 2, 'satuan' => 'set', 'harga_satuan' => 500000, 'keterangan' => 'Alat perah susu manual'],
            ]
        );

        $this->createLaporanRealisasi(
            $admin->id,
            $owner->id,
            6, // Juni
            2025,
            'peternakan',
            2, // minggu 2
            'Laporan realisasi minggu kedua Juni 2025 untuk divisi peternakan',
            [
                // Pendapatan
                ['kategori' => 'pendapatan', 'nama_item' => 'Susu Sapi', 'jumlah' => 160, 'satuan' => 'liter', 'harga_satuan' => 8000, 'keterangan' => 'Hasil perahan sapi perah'],
                ['kategori' => 'pendapatan', 'nama_item' => 'Kotoran Sapi', 'jumlah' => 200, 'satuan' => 'kg', 'harga_satuan' => 3000, 'keterangan' => 'Penjualan pupuk organik'],
                
                // Tenaga & Konsumsi
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Gaji Karyawan', 'jumlah' => 1, 'satuan' => 'bulan', 'harga_satuan' => 2500000, 'keterangan' => 'Gaji karyawan peternakan'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Pakan Sapi', 'jumlah' => 550, 'satuan' => 'kg', 'harga_satuan' => 5000, 'keterangan' => 'Pakan konsentrat sapi'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Rumput Hijau', 'jumlah' => 1100, 'satuan' => 'kg', 'harga_satuan' => 2000, 'keterangan' => 'Rumput hijau untuk pakan'],
                
                // Alat & Bahan
                ['kategori' => 'alat_bahan', 'nama_item' => 'Obat Cacing', 'jumlah' => 30, 'satuan' => 'botol', 'harga_satuan' => 25000, 'keterangan' => 'Obat cacing untuk sapi'],
                ['kategori' => 'alat_bahan', 'nama_item' => 'Tali Kandang', 'jumlah' => 50, 'satuan' => 'meter', 'harga_satuan' => 5000, 'keterangan' => 'Tali untuk kandang sapi'],
            ]
        );

        $this->createLaporanRealisasi(
            $admin->id,
            $owner->id,
            6, // Juni
            2025,
            'peternakan',
            3, // minggu 3
            'Laporan realisasi minggu ketiga Juni 2025 untuk divisi peternakan',
            [
                // Pendapatan
                ['kategori' => 'pendapatan', 'nama_item' => 'Susu Sapi', 'jumlah' => 155, 'satuan' => 'liter', 'harga_satuan' => 8000, 'keterangan' => 'Hasil perahan sapi perah'],
                ['kategori' => 'pendapatan', 'nama_item' => 'Daging Sapi', 'jumlah' => 30, 'satuan' => 'kg', 'harga_satuan' => 120000, 'keterangan' => 'Penjualan daging sapi potong'],
                
                // Tenaga & Konsumsi
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Gaji Karyawan', 'jumlah' => 1, 'satuan' => 'bulan', 'harga_satuan' => 2500000, 'keterangan' => 'Gaji karyawan peternakan'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Pakan Sapi', 'jumlah' => 520, 'satuan' => 'kg', 'harga_satuan' => 5000, 'keterangan' => 'Pakan konsentrat sapi'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Rumput Hijau', 'jumlah' => 1050, 'satuan' => 'kg', 'harga_satuan' => 2000, 'keterangan' => 'Rumput hijau untuk pakan'],
                
                // Alat & Bahan
                ['kategori' => 'alat_bahan', 'nama_item' => 'Obat Vitamin', 'jumlah' => 40, 'satuan' => 'botol', 'harga_satuan' => 15000, 'keterangan' => 'Vitamin untuk sapi'],
                ['kategori' => 'alat_bahan', 'nama_item' => 'Ember Susu', 'jumlah' => 10, 'satuan' => 'buah', 'harga_satuan' => 25000, 'keterangan' => 'Ember untuk menampung susu'],
            ]
        );

        $this->createLaporanRealisasi(
            $admin->id,
            $owner->id,
            6, // Juni
            2025,
            'peternakan',
            4, // minggu 4
            'Laporan realisasi minggu keempat Juni 2025 untuk divisi peternakan',
            [
                // Pendapatan
                ['kategori' => 'pendapatan', 'nama_item' => 'Susu Sapi', 'jumlah' => 165, 'satuan' => 'liter', 'harga_satuan' => 8000, 'keterangan' => 'Hasil perahan sapi perah'],
                ['kategori' => 'pendapatan', 'nama_item' => 'Kotoran Sapi', 'jumlah' => 250, 'satuan' => 'kg', 'harga_satuan' => 3000, 'keterangan' => 'Penjualan pupuk organik'],
                
                // Tenaga & Konsumsi
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Gaji Karyawan', 'jumlah' => 1, 'satuan' => 'bulan', 'harga_satuan' => 2500000, 'keterangan' => 'Gaji karyawan peternakan'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Pakan Sapi', 'jumlah' => 580, 'satuan' => 'kg', 'harga_satuan' => 5000, 'keterangan' => 'Pakan konsentrat sapi'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Rumput Hijau', 'jumlah' => 1200, 'satuan' => 'kg', 'harga_satuan' => 2000, 'keterangan' => 'Rumput hijau untuk pakan'],
                
                // Alat & Bahan
                ['kategori' => 'alat_bahan', 'nama_item' => 'Obat Cacing', 'jumlah' => 25, 'satuan' => 'botol', 'harga_satuan' => 25000, 'keterangan' => 'Obat cacing untuk sapi'],
                ['kategori' => 'alat_bahan', 'nama_item' => 'Sapu Lidi', 'jumlah' => 5, 'satuan' => 'buah', 'harga_satuan' => 15000, 'keterangan' => 'Sapu untuk membersihkan kandang'],
            ]
        );

        // Data untuk Juli 2025
        $this->createLaporanRealisasi(
            $admin->id,
            $owner->id,
            7, // Juli
            2025,
            'peternakan',
            1, // minggu 1
            'Laporan realisasi minggu pertama Juli 2025 untuk divisi peternakan',
            [
                // Pendapatan
                ['kategori' => 'pendapatan', 'nama_item' => 'Susu Sapi', 'jumlah' => 170, 'satuan' => 'liter', 'harga_satuan' => 8000, 'keterangan' => 'Hasil perahan sapi perah'],
                ['kategori' => 'pendapatan', 'nama_item' => 'Daging Sapi', 'jumlah' => 35, 'satuan' => 'kg', 'harga_satuan' => 120000, 'keterangan' => 'Penjualan daging sapi potong'],
                
                // Tenaga & Konsumsi
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Gaji Karyawan', 'jumlah' => 1, 'satuan' => 'bulan', 'harga_satuan' => 2500000, 'keterangan' => 'Gaji karyawan peternakan'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Pakan Sapi', 'jumlah' => 600, 'satuan' => 'kg', 'harga_satuan' => 5000, 'keterangan' => 'Pakan konsentrat sapi'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Rumput Hijau', 'jumlah' => 1300, 'satuan' => 'kg', 'harga_satuan' => 2000, 'keterangan' => 'Rumput hijau untuk pakan'],
                
                // Alat & Bahan
                ['kategori' => 'alat_bahan', 'nama_item' => 'Obat Vitamin', 'jumlah' => 60, 'satuan' => 'botol', 'harga_satuan' => 15000, 'keterangan' => 'Vitamin untuk sapi'],
                ['kategori' => 'alat_bahan', 'nama_item' => 'Alat Perah', 'jumlah' => 1, 'satuan' => 'set', 'harga_satuan' => 500000, 'keterangan' => 'Alat perah susu manual'],
            ]
        );

        $this->createLaporanRealisasi(
            $admin->id,
            $owner->id,
            7, // Juli
            2025,
            'peternakan',
            2, // minggu 2
            'Laporan realisasi minggu kedua Juli 2025 untuk divisi peternakan',
            [
                // Pendapatan
                ['kategori' => 'pendapatan', 'nama_item' => 'Susu Sapi', 'jumlah' => 175, 'satuan' => 'liter', 'harga_satuan' => 8000, 'keterangan' => 'Hasil perahan sapi perah'],
                ['kategori' => 'pendapatan', 'nama_item' => 'Kotoran Sapi', 'jumlah' => 300, 'satuan' => 'kg', 'harga_satuan' => 3000, 'keterangan' => 'Penjualan pupuk organik'],
                
                // Tenaga & Konsumsi
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Gaji Karyawan', 'jumlah' => 1, 'satuan' => 'bulan', 'harga_satuan' => 2500000, 'keterangan' => 'Gaji karyawan peternakan'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Pakan Sapi', 'jumlah' => 620, 'satuan' => 'kg', 'harga_satuan' => 5000, 'keterangan' => 'Pakan konsentrat sapi'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Rumput Hijau', 'jumlah' => 1350, 'satuan' => 'kg', 'harga_satuan' => 2000, 'keterangan' => 'Rumput hijau untuk pakan'],
                
                // Alat & Bahan
                ['kategori' => 'alat_bahan', 'nama_item' => 'Obat Cacing', 'jumlah' => 35, 'satuan' => 'botol', 'harga_satuan' => 25000, 'keterangan' => 'Obat cacing untuk sapi'],
                ['kategori' => 'alat_bahan', 'nama_item' => 'Tali Kandang', 'jumlah' => 60, 'satuan' => 'meter', 'harga_satuan' => 5000, 'keterangan' => 'Tali untuk kandang sapi'],
            ]
        );

        $this->createLaporanRealisasi(
            $admin->id,
            $owner->id,
            7, // Juli
            2025,
            'peternakan',
            3, // minggu 3
            'Laporan realisasi minggu ketiga Juli 2025 untuk divisi peternakan',
            [
                // Pendapatan
                ['kategori' => 'pendapatan', 'nama_item' => 'Susu Sapi', 'jumlah' => 180, 'satuan' => 'liter', 'harga_satuan' => 8000, 'keterangan' => 'Hasil perahan sapi perah'],
                ['kategori' => 'pendapatan', 'nama_item' => 'Daging Sapi', 'jumlah' => 40, 'satuan' => 'kg', 'harga_satuan' => 120000, 'keterangan' => 'Penjualan daging sapi potong'],
                
                // Tenaga & Konsumsi
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Gaji Karyawan', 'jumlah' => 1, 'satuan' => 'bulan', 'harga_satuan' => 2500000, 'keterangan' => 'Gaji karyawan peternakan'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Pakan Sapi', 'jumlah' => 650, 'satuan' => 'kg', 'harga_satuan' => 5000, 'keterangan' => 'Pakan konsentrat sapi'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Rumput Hijau', 'jumlah' => 1400, 'satuan' => 'kg', 'harga_satuan' => 2000, 'keterangan' => 'Rumput hijau untuk pakan'],
                
                // Alat & Bahan
                ['kategori' => 'alat_bahan', 'nama_item' => 'Obat Vitamin', 'jumlah' => 55, 'satuan' => 'botol', 'harga_satuan' => 15000, 'keterangan' => 'Vitamin untuk sapi'],
                ['kategori' => 'alat_bahan', 'nama_item' => 'Ember Susu', 'jumlah' => 8, 'satuan' => 'buah', 'harga_satuan' => 25000, 'keterangan' => 'Ember untuk menampung susu'],
            ]
        );

        $this->createLaporanRealisasi(
            $admin->id,
            $owner->id,
            7, // Juli
            2025,
            'peternakan',
            4, // minggu 4
            'Laporan realisasi minggu keempat Juli 2025 untuk divisi peternakan',
            [
                // Pendapatan
                ['kategori' => 'pendapatan', 'nama_item' => 'Susu Sapi', 'jumlah' => 185, 'satuan' => 'liter', 'harga_satuan' => 8000, 'keterangan' => 'Hasil perahan sapi perah'],
                ['kategori' => 'pendapatan', 'nama_item' => 'Kotoran Sapi', 'jumlah' => 350, 'satuan' => 'kg', 'harga_satuan' => 3000, 'keterangan' => 'Penjualan pupuk organik'],
                
                // Tenaga & Konsumsi
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Gaji Karyawan', 'jumlah' => 1, 'satuan' => 'bulan', 'harga_satuan' => 2500000, 'keterangan' => 'Gaji karyawan peternakan'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Pakan Sapi', 'jumlah' => 680, 'satuan' => 'kg', 'harga_satuan' => 5000, 'keterangan' => 'Pakan konsentrat sapi'],
                ['kategori' => 'tenaga_konsumsi', 'nama_item' => 'Rumput Hijau', 'jumlah' => 1500, 'satuan' => 'kg', 'harga_satuan' => 2000, 'keterangan' => 'Rumput hijau untuk pakan'],
                
                // Alat & Bahan
                ['kategori' => 'alat_bahan', 'nama_item' => 'Obat Cacing', 'jumlah' => 30, 'satuan' => 'botol', 'harga_satuan' => 25000, 'keterangan' => 'Obat cacing untuk sapi'],
                ['kategori' => 'alat_bahan', 'nama_item' => 'Sapu Lidi', 'jumlah' => 6, 'satuan' => 'buah', 'harga_satuan' => 15000, 'keterangan' => 'Sapu untuk membersihkan kandang'],
            ]
        );

        $this->command->info('Laporan realisasi Juni-Juli 2025 berhasil dibuat!');
    }

    private function createLaporanRealisasi($adminId, $ownerId, $bulan, $tahun, $divisi, $minggu, $keterangan, $items)
    {
        // Hitung total untuk setiap kategori
        $totalPendapatan = 0;
        $totalTenagaKonsumsi = 0;
        $totalAlatBahan = 0;

        foreach ($items as $item) {
            $total = $item['jumlah'] * $item['harga_satuan'];
            switch ($item['kategori']) {
                case 'pendapatan':
                    $totalPendapatan += $total;
                    break;
                case 'tenaga_konsumsi':
                    $totalTenagaKonsumsi += $total;
                    break;
                case 'alat_bahan':
                    $totalAlatBahan += $total;
                    break;
            }
        }

        $totalBiaya = $totalTenagaKonsumsi + $totalAlatBahan;

        // Buat laporan realisasi
        $laporanRealisasi = LaporanRealisasi::create([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'divisi' => $divisi,
            'minggu' => $minggu,
            'tanggal' => Carbon::create($tahun, $bulan, 15),
            'total_pendapatan' => $totalPendapatan,
            'total_tenaga_konsumsi' => $totalTenagaKonsumsi,
            'total_alat_bahan' => $totalAlatBahan,
            'total_biaya' => $totalBiaya,
            'keterangan' => $keterangan,
            'status' => 'approved',
            'submitted_by' => $adminId,
            'approved_by' => $ownerId,
            'tanggal_approval' => Carbon::create($tahun, $bulan, 20),
        ]);

        // Buat item-item laporan
        foreach ($items as $item) {
            LaporanRealisasiItem::create([
                'laporan_realisasi_id' => $laporanRealisasi->id,
                'kategori' => $item['kategori'],
                'nama_item' => $item['nama_item'],
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'keterangan' => $item['keterangan'],
                'minggu' => (int) $minggu,
            ]);
        }

        $this->command->info("Laporan realisasi {$divisi} {$minggu} {$bulan} {$tahun} berhasil dibuat!");
    }
}
