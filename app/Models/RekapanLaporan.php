<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\PengajuanDana;

class RekapanLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_bulan',
        'periode_tahun',
        'divisi',
        'total_pendapatan',
        'total_tenaga_konsumsi',
        'total_alat_bahan',
        'total_biaya',
        'generated_by',
        'generated_at',
        'keterangan',
    ];

    protected $casts = [
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
        'total_pendapatan' => 'decimal:2',
        'total_tenaga_konsumsi' => 'decimal:2',
        'total_alat_bahan' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'generated_at' => 'datetime',
    ];


    // Divisi constants
    const DIVISI_PETERNAKAN = 'peternakan';
    const DIVISI_PERKEBUNAN = 'perkebunan';

    /**
     * Get the user who generated the rekapan
     */
    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get the rekapan items
     */
    public function items()
    {
        return $this->hasMany(RekapanLaporanItem::class);
    }


    /**
     * Get divisi label
     */
    public function getDivisiLabel()
    {
        switch ($this->divisi) {
            case self::DIVISI_PETERNAKAN:
                return 'Peternakan';
            case self::DIVISI_PERKEBUNAN:
                return 'Perkebunan';
            case 'combined':
                return 'Semua Divisi';
            default:
                return ucfirst($this->divisi);
        }
    }

    /**
     * Get periode label
     */
    public function getPeriodeLabel()
    {
        $bulan = Carbon::create()->month($this->periode_bulan)->format('F');
        return $bulan . ' ' . $this->periode_tahun;
    }

    /**
     * Get bulan label
     */
    public function getBulanLabel()
    {
        return Carbon::create()->month($this->periode_bulan)->format('F');
    }


    /**
     * Calculate totals from items
     */
    public function calculateTotals()
    {
        $this->total_pendapatan = (float) ($this->items()->where('kategori', 'pendapatan')->sum(\DB::raw('jumlah * harga_satuan')) ?? 0.0);
        $this->total_tenaga_konsumsi = (float) ($this->items()->where('kategori', 'tenaga_konsumsi')->sum(\DB::raw('jumlah * harga_satuan')) ?? 0.0);
        $this->total_alat_bahan = (float) ($this->items()->where('kategori', 'alat_bahan')->sum(\DB::raw('jumlah * harga_satuan')) ?? 0.0);
        $this->total_biaya = $this->total_tenaga_konsumsi + $this->total_alat_bahan;
        $this->save();
    }

    /**
     * Compute debit (pencairan dana) for this rekapan's period/divisi
     * Always calculate real-time from source data
     */
    public function getDebitAmount(): float
    {
        return (float) self::computeDebit(
            (int) $this->periode_bulan,
            (int) $this->periode_tahun,
            $this->divisi
        );
    }

    /**
     * Compute debit amount by period/divisi without altering schema
     */
    public static function computeDebit(int $bulan, int $tahun, string $divisi = 'combined'): float
    {
        $query = PengajuanDana::where('status', PengajuanDana::STATUS_REALIZED)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with('items');

        if ($divisi !== 'combined') {
            $query->where('divisi', $divisi);
        }

        $pengajuans = $query->get();

        // Return 0 if no data found
        if ($pengajuans->isEmpty()) {
            return 0.0;
        }

        $total = 0.0;
        foreach ($pengajuans as $p) {
            foreach ($p->items as $item) {
                $total += (float) $item->jumlah * (float) $item->harga_satuan;
            }
        }
        return (float) $total;
    }

    /**
     * Compute kredit amount by period/divisi from laporan realisasi
     * Kredit hanya dari item biaya (bukan pendapatan)
     */
    public static function computeKredit(int $bulan, int $tahun, string $divisi = 'combined'): float
    {
        $query = LaporanRealisasi::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with('items');

        if ($divisi !== 'combined') {
            $query->where('divisi', $divisi);
        }

        $laporanRealisasis = $query->get();

        // Return 0 if no data found
        if ($laporanRealisasis->isEmpty()) {
            return 0.0;
        }

        $total = 0.0;
        foreach ($laporanRealisasis as $laporan) {
            foreach ($laporan->items as $item) {
                // Hanya ambil item biaya, bukan pendapatan
                if (in_array($item->kategori, ['tenaga_konsumsi', 'alat_bahan', 'biaya'])) {
                    $total += (float) $item->jumlah * (float) $item->harga_satuan;
                }
            }
        }
        return (float) $total;
    }

    /**
     * Compute pendapatan amount by period and optionally divisi.
     * Jika divisi 'combined', agregasi seluruh divisi.
     */
    public static function computePendapatan(int $bulan, int $tahun, string $divisi = 'combined'): float
    {
        // Pendapatan saat ini berasal dari PendapatanSusu dengan kolom kategori sebagai pengganti divisi
        $query = PendapatanSusu::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($divisi !== 'combined') {
            // Mapping: kategori pendapatan == divisi
            $query->where('kategori', $divisi);
        }

        $totalPendapatan = $query->sum('total_pendapatan');

        return (float) ($totalPendapatan ?? 0.0);
    }

    /**
     * Refresh rekapan data for a specific period and divisi
     * This will recalculate and update the stored values
     */
    public static function refreshRekapan(int $bulan, int $tahun, string $divisi = 'combined'): void
    {
        $rekapan = self::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('divisi', $divisi)
            ->first();

        if ($rekapan) {
            // Update with real-time calculated values
            $rekapan->total_pendapatan = self::computePendapatan($bulan, $tahun, $divisi);
            $rekapan->total_biaya = self::computeKredit($bulan, $tahun, $divisi);
            $rekapan->generated_at = now();
            $rekapan->save();
        }
    }

    /**
     * Kredit adalah total biaya pada rekapan ini
     * Always calculate real-time from laporan realisasi
     */
    public function getKreditAmount(): float
    {
        return (float) self::computeKredit(
            (int) $this->periode_bulan,
            (int) $this->periode_tahun,
            $this->divisi
        );
    }

    /**
     * Saldo = Debit - Kredit
     */
    public function getSaldoAmount(): float
    {
        return (float) ($this->getDebitAmount() - $this->getKreditAmount());
    }

    /**
     * Generate rekapan from approved laporan realisasi
     */
    public static function generateFromApprovedLaporan($bulan, $tahun, $divisi, $userId)
    {
        // Jika sudah ada rekapan untuk periode/divisi ini, kembalikan atau perbarui tanpa membuat duplikat
        $existingRekapan = self::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('divisi', $divisi)
            ->first();

        // Get all laporan realisasi for the specified period and divisi (no status check needed)
        $approvedLaporan = LaporanRealisasi::with(['items'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('divisi', $divisi)
            ->get();

        if ($approvedLaporan->isEmpty()) {
            // Jika tidak ada data, pastikan tidak membuat duplikat - gunakan firstOrCreate
            return self::firstOrCreate(
                [
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                    'divisi' => $divisi,
                ],
                [
                    'total_pendapatan' => 0.0,
                    'total_tenaga_konsumsi' => 0.0,
                    'total_alat_bahan' => 0.0,
                    'total_biaya' => 0.0,
                    'generated_by' => $userId,
                    'generated_at' => now(),
                ]
            );
        }

        if ($existingRekapan) {
            // Update existing rekapan dan hapus items lama sebelum isi ulang
            $rekapan = $existingRekapan;
            $rekapan->items()->delete();
        } else {
            // Create new rekapan
            $rekapan = self::create([
                'periode_bulan' => $bulan,
                'periode_tahun' => $tahun,
                'divisi' => $divisi,
                'generated_by' => $userId,
                'generated_at' => now(),
                'keterangan' => 'Rekapan otomatis dari laporan realisasi',
            ]);
        }

        // Collect all items from approved laporan
        $allItems = collect();
        foreach ($approvedLaporan as $laporan) {
            foreach ($laporan->items as $item) {
                $allItems->push([
                    'rekapan_laporan_id' => $rekapan->id,
                    'kategori' => $item->kategori,
                    'nama_item' => $item->nama_item,
                    'jumlah' => $item->jumlah,
                    'satuan' => $item->satuan,
                    'harga_satuan' => $item->harga_satuan,
                    'keterangan' => $item->keterangan,
                    'minggu' => $item->minggu,
                    'laporan_realisasi_id' => $laporan->id,
                ]);
            }
        }

        // Group items by kategori and nama_item, then sum the amounts
        $groupedItems = $allItems->groupBy(function ($item) {
            return $item['kategori'] . '|' . $item['nama_item'] . '|' . $item['satuan'] . '|' . $item['harga_satuan'];
        })->map(function ($group) {
            $firstItem = $group->first();
            return [
                'rekapan_laporan_id' => $firstItem['rekapan_laporan_id'],
                'kategori' => $firstItem['kategori'],
                'nama_item' => $firstItem['nama_item'],
                'jumlah' => $group->sum('jumlah'),
                'satuan' => $firstItem['satuan'],
                'harga_satuan' => $firstItem['harga_satuan'],
                'keterangan' => $firstItem['keterangan'],
                'minggu' => null, // Not applicable for rekapan
                'laporan_realisasi_id' => null, // Not applicable for rekapan
            ];
        });

        // Create rekapan items
        foreach ($groupedItems as $item) {
            RekapanLaporanItem::create($item);
        }

        // Calculate totals
        $rekapan->calculateTotals();

        return $rekapan;
    }

    /**
     * Generate combined rekapan (all divisions) for a month and year
     */
    public static function generateCombinedForMonth(int $bulan, int $tahun, int $userId)
    {
        $approvedLaporan = LaporanRealisasi::with(['items'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        if ($approvedLaporan->isEmpty()) {
            // Gunakan firstOrCreate agar tidak duplikasi saat kosong
            return self::firstOrCreate(
                [
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                    'divisi' => 'combined',
                ],
                [
                    'total_pendapatan' => 0.0,
                    'total_tenaga_konsumsi' => 0.0,
                    'total_alat_bahan' => 0.0,
                    'total_biaya' => 0.0,
                    'generated_by' => $userId,
                    'generated_at' => now(),
                    'keterangan' => 'Rekapan gabungan kosong - tidak ada laporan realisasi',
                ]
            );
        }

        // Check if combined rekapan already exists
        $existing = self::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('divisi', 'combined')
            ->first();

        if ($existing) {
            $rekapan = $existing;
            $rekapan->items()->delete();
        } else {
            $rekapan = self::create([
                'periode_bulan' => $bulan,
                'periode_tahun' => $tahun,
                'divisi' => 'combined',
                'generated_by' => $userId,
                'generated_at' => now(),
                'keterangan' => 'Rekapan gabungan otomatis dari laporan realisasi',
            ]);
        }

        $allItems = collect();
        foreach ($approvedLaporan as $laporan) {
            foreach ($laporan->items as $item) {
                $allItems->push([
                    'rekapan_laporan_id' => $rekapan->id,
                    'kategori' => $item->kategori,
                    'nama_item' => $item->nama_item,
                    'jumlah' => $item->jumlah,
                    'satuan' => $item->satuan,
                    'harga_satuan' => $item->harga_satuan,
                    'keterangan' => $item->keterangan,
                    'minggu' => $item->minggu,
                    'laporan_realisasi_id' => $laporan->id,
                ]);
            }
        }

        $grouped = $allItems->groupBy(function ($item) {
            return $item['kategori'] . '|' . $item['nama_item'] . '|' . $item['satuan'] . '|' . $item['harga_satuan'];
        })->map(function ($group) {
            $first = $group->first();
            return [
                'rekapan_laporan_id' => $first['rekapan_laporan_id'],
                'kategori' => $first['kategori'],
                'nama_item' => $first['nama_item'],
                'jumlah' => $group->sum('jumlah'),
                'satuan' => $first['satuan'],
                'harga_satuan' => $first['harga_satuan'],
                'keterangan' => $first['keterangan'],
                'minggu' => null,
                'laporan_realisasi_id' => null,
            ];
        });

        foreach ($grouped as $item) {
            RekapanLaporanItem::create($item);
        }

        $rekapan->calculateTotals();
        return $rekapan;
    }
}
