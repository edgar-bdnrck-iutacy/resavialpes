<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\AlertService;

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
    public function boot()
    {
        View::composer('*', function ($view) {
            $service = app(AlertService::class);
            $alerts  = $service->all();
        
            $view->with([
                'alerts'          => $alerts,
                'alertsCount'     => count($alerts),
                'alertsMaxLevel'  => $service->maxLevel($alerts),
            ]);
        });
    }
}
