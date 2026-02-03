<?php

namespace App\Providers;

use App\Events\LaporanRealisasiUpdated;
use App\Events\PendapatanSusuUpdated;
use App\Listeners\UpdateRekapanLaporan;
use App\Listeners\UpdateRekapanFromPendapatan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listeners
        Event::listen(
            LaporanRealisasiUpdated::class,
            UpdateRekapanLaporan::class
        );
        
        Event::listen(
            PendapatanSusuUpdated::class,
            UpdateRekapanFromPendapatan::class
        );
    }
}
