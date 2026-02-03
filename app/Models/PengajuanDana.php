<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDana extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'divisi',
        'bulan',
        'tahun',
        'status',
        'submitted_by',
        'approved_by',
        'rejected_by',
        'alasan_rejection',
        'tanggal_approval',
        'realized_by',
        'tanggal_realisasi',
        'nominal_diberikan',
        'keterangan',
        'bukti_transfer',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_approval' => 'datetime',
        'tanggal_realisasi' => 'datetime',
        'nominal_diberikan' => 'decimal:2',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMIT = 'submit';
    const STATUS_APPROVED = 'approved';
    const STATUS_REALIZED = 'realized';
    const STATUS_REJECTED = 'rejected';

    // Divisi constants
    const DIVISI_PETERNAKAN = 'peternakan';
    const DIVISI_PERKEBUNAN = 'perkebunan';

    // Jenis kebutuhan constants
    const JENIS_OPERASIONAL = 'operasional';
    const JENIS_GAJI = 'gaji';
    const JENIS_KONSUMSI = 'konsumsi';
    const JENIS_LAINNYA = 'lainnya';

    /**
     * Get the user who submitted the pengajuan
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved the pengajuan
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the pengajuan
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who realized the pengajuan
     */
    public function realizedBy()
    {
        return $this->belongsTo(User::class, 'realized_by');
    }

    /**
     * Get the pengajuan items
     */
    public function items()
    {
        return $this->hasMany(PengajuanDanaItem::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
                return 'Draft';
            case self::STATUS_SUBMIT:
                return 'Menunggu Persetujuan';
            case self::STATUS_APPROVED:
                return 'Disetujui';
            case self::STATUS_REJECTED:
                return 'Ditolak';
            case self::STATUS_REALIZED:
                return 'Dana Diberikan';
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
            case self::STATUS_DRAFT:
                return 'bg-gray-100 text-gray-800';
            case self::STATUS_SUBMIT:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_APPROVED:
                return 'bg-green-100 text-green-800';
            case self::STATUS_REJECTED:
                return 'bg-red-100 text-red-800';
            case self::STATUS_REALIZED:
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
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
     * Get bulan label
     */
    public function getBulanLabel()
    {
        $bulanNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        return $bulanNames[$this->bulan] ?? 'Bulan ' . $this->bulan;
    }


    /**
     * Calculate total amount from items
     */
    public function getTotalAmount()
    {
        return $this->items->sum(function($item) {
            return $item->jumlah * $item->harga_satuan;
        });
    }

    /**
     * Check if pengajuan is draft
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if pengajuan is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_SUBMIT;
    }

    /**
     * Check if pengajuan is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if pengajuan is rejected
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if pengajuan is realized
     */
    public function isRealized()
    {
        return $this->status === self::STATUS_REALIZED;
    }
}
