<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PengajuanDana;
use App\Models\PengajuanDanaItem;
use App\Models\User;
use Carbon\Carbon;

class PengajuanDanaTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first();
        $owner = User::where('role', 'owner')->first();
        
        if (!$admin) {
            $this->command->error('Admin user not found. Please create admin user first.');
            return;
        }

        $tahun = now()->year;
        $bulanList = [6, 7]; // Juni, Juli
        $divisis = ['peternakan', 'perkebunan'];
        $statusList = ['draft', 'submit', 'approved', 'realized'];
        
        foreach ($bulanList as $bulan) {
            foreach ($divisis as $divisi) {
                $tanggal = Carbon::create($tahun, $bulan, 1);
                $status = $statusList[($bulan - 6) % count($statusList)];
                $pengajuan = PengajuanDana::create([
                    'tanggal' => $tanggal,
                    'divisi' => $divisi,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'status' => $status,
                    'submitted_by' => $admin->id,
                    'approved_by' => $owner ? $owner->id : null,
                    'tanggal_approval' => $status === 'approved' ? $tanggal->copy()->addDays(2) : null,
                    'keterangan' => "Pengajuan dana {$divisi} bulan {$bulan}",
                ]);
                    // Tambah item dummy
                    $items = [
                        [
                            'jenis_kebutuhan' => 'operasional',
                            'nama_kebutuhan' => 'Pakan Ternak',
                            'jumlah' => 100 + $bulan,
                            'satuan' => 'kg',
                            'harga_satuan' => 5000,
                            'keterangan' => 'Pakan untuk ternak',
                        ],
                        [
                            'jenis_kebutuhan' => 'gaji',
                            'nama_kebutuhan' => 'Gaji Karyawan',
                            'jumlah' => 10,
                            'satuan' => 'orang',
                            'harga_satuan' => 1000000,
                            'keterangan' => 'Gaji bulanan',
                        ],
                        [
                            'jenis_kebutuhan' => 'konsumsi',
                            'nama_kebutuhan' => 'Konsumsi Karyawan',
                            'jumlah' => 10,
                            'satuan' => 'porsi',
                            'harga_satuan' => 20000,
                            'keterangan' => 'Makan siang',
                        ],
                    ];
                    foreach ($items as $item) {
                        PengajuanDanaItem::create([
                            'pengajuan_dana_id' => $pengajuan->id,
                            'jenis_kebutuhan' => $item['jenis_kebutuhan'],
                            'nama_kebutuhan' => $item['nama_kebutuhan'],
                            'jumlah' => $item['jumlah'],
                            'satuan' => $item['satuan'],
                            'harga_satuan' => $item['harga_satuan'],
                            'keterangan' => $item['keterangan'],
                        ]);
                    }
            }
        }
        $this->command->info('Pengajuan dana untuk Juni-Juli per bulan berhasil dibuat!');
    }
}
