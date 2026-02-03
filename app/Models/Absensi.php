<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
    ];

    // Status constants
    const STATUS_HADIR = 'hadir';
    const STATUS_IZIN = 'izin';
    const STATUS_ALFA = 'alpha'; // Database uses 'alpha', not 'alfa'

    /**
     * Get the pegawai that owns the absensi.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Check if absensi is hadir
     */
    public function isHadir()
    {
        return $this->status === self::STATUS_HADIR;
    }

    /**
     * Check if absensi is izin
     */
    public function isIzin()
    {
        return $this->status === self::STATUS_IZIN;
    }

    /**
     * Check if absensi is alfa
     */
    public function isAlfa()
    {
        return $this->status === self::STATUS_ALFA;
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        switch ($this->status) {
            case self::STATUS_HADIR:
                return 'Hadir';
            case self::STATUS_IZIN:
                return 'Izin';
            case self::STATUS_ALFA:
                return 'Alfa';
            case 'sakit':
                return 'Sakit';
            default:
                return ucfirst($this->status);
        }
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case self::STATUS_HADIR:
                return 'bg-green-100 text-green-800';
            case self::STATUS_IZIN:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_ALFA:
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}
