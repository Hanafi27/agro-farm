<?php

namespace App\Events;

use App\Models\LaporanRealisasi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LaporanRealisasiUpdated
{
    use Dispatchable, SerializesModels;

    public $laporanRealisasi;
    public $action; // 'created', 'updated', 'deleted', 'item_deleted'

    /**
     * Create a new event instance.
     */
    public function __construct(LaporanRealisasi $laporanRealisasi, string $action = 'updated')
    {
        $this->laporanRealisasi = $laporanRealisasi;
        $this->action = $action;
    }
}
