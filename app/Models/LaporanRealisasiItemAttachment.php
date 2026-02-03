<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanRealisasiItemAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_realisasi_item_id',
        'path',
        'filename',
        'extension',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function item()
    {
        return $this->belongsTo(LaporanRealisasiItem::class, 'laporan_realisasi_item_id');
    }
}


