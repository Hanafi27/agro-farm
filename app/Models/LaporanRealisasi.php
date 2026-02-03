<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanRealisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'divisi',
        'minggu',
        'bulan',
        'tahun',
        'submitted_by',
        'keterangan',
        'total_pendapatan',
        'total_tenaga_konsumsi',
        'total_alat_bahan',
        'total_biaya',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_pendapatan' => 'decimal:2',
        'total_tenaga_konsumsi' => 'decimal:2',
        'total_alat_bahan' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    // Divisi constants
    const DIVISI_PETERNAKAN = 'peternakan';
    const DIVISI_PERKEBUNAN = 'perkebunan';

    // Kategori constants
    const KATEGORI_PENDAPATAN = 'pendapatan';
    const KATEGORI_TENAGA_KONSUMSI = 'tenaga_konsumsi';
    const KATEGORI_ALAT_BAHAN = 'alat_bahan';

    /**
     * Get the user who submitted the laporan
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }


    /**
     * Get the laporan items
     */
    public function items()
    {
        return $this->hasMany(LaporanRealisasiItem::class);
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
            default:
                return ucfirst($this->divisi);
        }
    }

    /**
     * Get minggu label
     */
    public function getMingguLabel()
    {
        return "Week {$this->minggu}";
    }

    /**
     * Get bulan label
     */
    public function getBulanLabel()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? $this->bulan;
    }

    /**
     * Calculate total from items
     */
    public function calculateTotals()
    {
        $this->total_pendapatan = $this->items()->where('kategori', self::KATEGORI_PENDAPATAN)->sum(\DB::raw('jumlah * harga_satuan'));
        $this->total_tenaga_konsumsi = $this->items()->where('kategori', self::KATEGORI_TENAGA_KONSUMSI)->sum(\DB::raw('jumlah * harga_satuan'));
        $this->total_alat_bahan = $this->items()->where('kategori', self::KATEGORI_ALAT_BAHAN)->sum(\DB::raw('jumlah * harga_satuan'));
        $this->total_biaya = $this->total_tenaga_konsumsi + $this->total_alat_bahan;
        $this->save();
    }

}
