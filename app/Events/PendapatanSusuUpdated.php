<?php

namespace App\Events;

use App\Models\PendapatanSusu;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PendapatanSusuUpdated
{
    use Dispatchable, SerializesModels;

    public $pendapatanSusu;
    public $action; // 'created', 'updated', 'deleted'

    /**
     * Create a new event instance.
     */
    public function __construct(PendapatanSusu $pendapatanSusu, string $action = 'updated')
    {
        $this->pendapatanSusu = $pendapatanSusu;
        $this->action = $action;
    }
}
