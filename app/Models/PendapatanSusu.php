<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendapatanSusu extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'kategori',
        'jenis_produk',
        'jumlah_liter',
        'satuan',
        'harga_per_liter',
        'total_pendapatan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_liter' => 'integer',
        'harga_per_liter' => 'integer',
        'total_pendapatan' => 'integer',
    ];

    // Kategori constants
    const KATEGORI_PERKEBUNAN = 'perkebunan';
    const KATEGORI_PETERNAKAN = 'peternakan';

    // Jenis produk constants
    const JENIS_TEH = 'teh';
    const JENIS_SUSU_KAMBING = 'susu_kambing';
    const JENIS_SUSU_SAPI = 'susu_sapi';

    /**
     * Get kategori label
     */
    public function getKategoriLabel()
    {
        switch ($this->kategori) {
            case self::KATEGORI_PERKEBUNAN:
                return 'Perkebunan';
            case self::KATEGORI_PETERNAKAN:
                return 'Peternakan';
            default:
                return ucfirst($this->kategori);
        }
    }

    /**
     * Get jenis produk label
     */
    public function getJenisProdukLabel()
    {
        switch ($this->jenis_produk) {
            case self::JENIS_TEH:
                return 'Teh';
            case self::JENIS_SUSU_KAMBING:
                return 'Susu Kambing';
            case self::JENIS_SUSU_SAPI:
                return 'Susu Sapi';
            default:
                return ucfirst($this->jenis_produk);
        }
    }

    /**
     * Get kategori badge class
     */
    public function getKategoriBadgeClass()
    {
        switch ($this->kategori) {
            case self::KATEGORI_PERKEBUNAN:
                return 'bg-green-100 text-green-800';
            case self::KATEGORI_PETERNAKAN:
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Get jenis produk badge class
     */
    public function getJenisProdukBadgeClass()
    {
        switch ($this->jenis_produk) {
            case self::JENIS_TEH:
                return 'bg-yellow-100 text-yellow-800';
            case self::JENIS_SUSU_KAMBING:
                return 'bg-orange-100 text-orange-800';
            case self::JENIS_SUSU_SAPI:
                return 'bg-purple-100 text-purple-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}
