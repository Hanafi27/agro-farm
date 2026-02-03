<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'divisi',
        'kontak',
        'alamat',
        'gaji_pokok',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
    ];

    /**
     * Get the user that owns the pegawai.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the absensi for the pegawai.
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Get the penggajian for the pegawai.
     */
    public function penggajians()
    {
        return $this->hasMany(Penggajian::class);
    }

    /**
     * Get the pendapatan susu for the pegawai.
     */
    public function pendapatanSusus()
    {
        return $this->hasMany(PendapatanSusu::class);
    }

    /**
     * Get the pengajuan dana for the pegawai.
     */
    public function pengajuanDanas()
    {
        return $this->hasMany(PengajuanDana::class);
    }
}
