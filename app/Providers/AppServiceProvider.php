<?php

namespace App\Providers;

use App\Models\Supply;
use App\Observers\SupplyObserver;
use App\Services\SupplyMonitoringService;
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

    }
}
