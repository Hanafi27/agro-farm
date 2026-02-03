<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapanLaporanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rekapan_laporan_id',
        'kategori',
        'nama_item',
        'jumlah',
        'satuan',
        'harga_satuan',
        'keterangan',
        'minggu',
        'laporan_realisasi_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
    ];

    // Kategori constants
    const KATEGORI_PENDAPATAN = 'pendapatan';
    const KATEGORI_TENAGA_KONSUMSI = 'tenaga_konsumsi';
    const KATEGORI_ALAT_BAHAN = 'alat_bahan';

    /**
     * Get the rekapan laporan that owns the item
     */
    public function rekapanLaporan()
    {
        return $this->belongsTo(RekapanLaporan::class);
    }

    /**
     * Get kategori label
     */
    public function getKategoriLabel()
    {
        switch ($this->kategori) {
            case self::KATEGORI_PENDAPATAN:
                return 'Pendapatan';
            case self::KATEGORI_TENAGA_KONSUMSI:
                return 'Tenaga Kerja & Konsumsi';
            case self::KATEGORI_ALAT_BAHAN:
                return 'Alat & Bahan';
            default:
                return ucfirst($this->kategori);
        }
    }

    /**
     * Calculate total amount for this item
     */
    public function getTotalAmount()
    {
        return $this->jumlah * $this->harga_satuan;
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmount()
    {
        return 'Rp ' . number_format($this->getTotalAmount(), 0, ',', '.');
    }
}
