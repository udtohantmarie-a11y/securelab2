<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        /**
         * Pinipilit ang Laravel na gumamit ng HTTPS scheme.
         * * Napaka-importante nito kapag gumagamit ng Ngrok para maiwasan ang "Mixed Content" errors.
         * Kung ang assets (CSS, JS, Images) ay naka-HTTP habang ang site ay HTTPS,
         * bina-block ito ng browser, kaya hindi lumilitaw ang mga pictures o styles.
         * * Nakatutulong din ito para maging 'Installable' ang iyong PWA sa mga mobile devices
         * dahil required ang HTTPS para sa Service Workers.
         */
        
        // I-force ang HTTPS kung hindi local ang environment
        // O kung ang request ay galing sa isang Ngrok tunnel
        if (config('app.env') !== 'local' || str_contains(request()->getHost(), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }
    }
}