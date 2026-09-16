<?php

namespace App\Providers;

use App\Models\Configuracion;
use App\Models\MateriaPrima;
use App\Models\Receta;
use App\Models\Usuario;
use App\Observers\MateriaPrimaObserver;
use App\Observers\RecetaObserver;
use App\Services\ComandaService;
use App\Services\CreditoService;
use App\Services\PrecioService;
use App\Services\PuntosService;
use App\Services\RecetaCostoService;
use App\Services\ReporteService;
use App\Services\StockService;
use App\Services\TasaBcvService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TasaBcvService::class);
        $this->app->singleton(PrecioService::class);
        $this->app->singleton(StockService::class);
        $this->app->singleton(PuntosService::class);
        $this->app->singleton(CreditoService::class);
        $this->app->singleton(ComandaService::class);
        $this->app->singleton(ReporteService::class);
        $this->app->singleton(RecetaCostoService::class);
    }

    public function boot(): void
    {
        Receta::observe(RecetaObserver::class);
        MateriaPrima::observe(MateriaPrimaObserver::class);

        Gate::define('permiso', function (Usuario $user, string $permiso) {
            return $user->rol && $user->rol->permisos->contains('codigo', $permiso);
        });

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
            $tasa = Configuracion::obtener('tasa_bcv', '818.00');
            $view->with('tasaBcv', $tasa);
        });
    }
}
