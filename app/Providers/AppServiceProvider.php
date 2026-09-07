<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\TasaBcvService::class);
        $this->app->singleton(\App\Services\PrecioService::class);
        $this->app->singleton(\App\Services\StockService::class);
        $this->app->singleton(\App\Services\PuntosService::class);
        $this->app->singleton(\App\Services\CreditoService::class);
        $this->app->singleton(\App\Services\ComandaService::class);
        $this->app->singleton(\App\Services\ReporteService::class);
    }

    public function boot(): void
    {
        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(5)->by($request->username ?? $request->ip());
        });

        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?? $request->ip());
        });

        RateLimiter::for('crear-comanda', function ($request) {
            return Limit::perMinute(30)->by($request->user()?->id ?? $request->ip());
        });

        RateLimiter::for('exportar', function ($request) {
            return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
        });

        RateLimiter::for('reportes', function ($request) {
            return Limit::perMinute(20)->by($request->user()?->id ?? $request->ip());
        });

        RateLimiter::for('catalogo', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?? $request->ip());
        });

        View::composer('layouts.partials.topbar', function ($view) {
            $tasa = \App\Models\Configuracion::obtener('tasa_bcv', '42.50');
            $view->with('tasaBcv', $tasa);
        });
    }
}
