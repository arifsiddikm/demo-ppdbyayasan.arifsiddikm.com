<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix setLocale warning di Laravel terbaru
        try {
            Carbon::setLocale(config('app.locale', 'id'));
            setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');
        } catch (\Exception $e) {
            // ignore locale errors on hosting
        }

        // FIX #1: Force HTTPS di production (shared hosting sering pakai proxy)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            // Agar asset() dan route() juga pakai https
            URL::forceRootUrl(config('app.url'));
        }

        // FIX #2: Default string length untuk MySQL < 5.7.7 / MariaDB lama
        Schema::defaultStringLength(191);
    }
}
