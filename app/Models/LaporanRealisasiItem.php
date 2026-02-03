<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanRealisasiItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_realisasi_id',
        'kategori',
        'nama_item',
        'jumlah',
        'jumlah_realisasi',
        'satuan',
        'harga_satuan',
        'keterangan',
        'nota',
        'keterangan_realisasi',
        'minggu', // tambahkan minggu agar bisa mass assignment
        'pengajuan_dana_item_id', // referensi item pengajuan (opsional)
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'jumlah_realisasi' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
    ];

    // Kategori constants
    const KATEGORI_PENDAPATAN = 'pendapatan';
    const KATEGORI_TENAGA_KONSUMSI = 'tenaga_konsumsi';
    const KATEGORI_ALAT_BAHAN = 'alat_bahan';

    /**
     * Get the laporan realisasi that owns the item
     */
    public function laporanRealisasi()
    {
        return $this->belongsTo(LaporanRealisasi::class);
    }

    /**
     * Relasi ke item pada pengajuan dana (jika item ini berasal dari pengajuan)
     */
    public function pengajuanDanaItem()
    {
        return $this->belongsTo(PengajuanDanaItem::class, 'pengajuan_dana_item_id');
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

    /**
     * Get formatted harga satuan
     */
    public function getFormattedHargaSatuan()
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    /**
     * Get formatted jumlah
     */
    public function getFormattedJumlah()
    {
        return number_format(round($this->jumlah), 0, ',', '');
    }

    /**
     * Attachments (multi-file proofs)
     */
    public function attachments()
    {
        return $this->hasMany(LaporanRealisasiItemAttachment::class, 'laporan_realisasi_item_id');
    }
} 