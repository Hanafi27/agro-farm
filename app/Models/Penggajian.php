<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'bulan',
        'tahun',
        'tipe_periode',
        'tanggal',
        'gaji_per_bulan',
        'gaji_per_minggu',
        'total_hadir',
        'total_izin',
        'total_alfa',
        'potongan',
        'total_gaji',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'gaji_per_bulan' => 'integer',
        'gaji_per_minggu' => 'integer',
        'total_hadir' => 'integer',
        'total_izin' => 'integer',
        'total_alfa' => 'integer',
        'potongan' => 'integer',
        'total_gaji' => 'integer',
    ];

    /**
     * Get the pegawai that owns the penggajian.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
