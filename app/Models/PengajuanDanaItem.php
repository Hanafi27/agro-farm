<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDanaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_dana_id',
        'jenis_kebutuhan',
        'nama_kebutuhan',
        'jumlah',
        'satuan',
        'harga_satuan',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
    ];

    // Jenis kebutuhan constants
    const JENIS_OPERASIONAL = 'operasional';
    const JENIS_GAJI = 'gaji';
    const JENIS_KONSUMSI = 'konsumsi';
    const JENIS_LAINNYA = 'lainnya';

    /**
     * Get the pengajuan dana that owns the item
     */
    public function pengajuanDana()
    {
        return $this->belongsTo(PengajuanDana::class);
    }

    /**
     * Get jenis kebutuhan label
     */
    public function getJenisKebutuhanLabel()
    {
        switch ($this->jenis_kebutuhan) {
            case self::JENIS_OPERASIONAL:
                return 'Operasional';
            case self::JENIS_GAJI:
                return 'Gaji';
            case self::JENIS_KONSUMSI:
                return 'Konsumsi';
            case self::JENIS_LAINNYA:
                return 'Lainnya';
            default:
                return ucfirst($this->jenis_kebutuhan);
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
}
