<?php

namespace App\Traits;

use App\Services\AuditoriaService;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            if (auth()->check()) {
                AuditoriaService::registrar(
                    auth()->user(),
                    'crear',
                    $model,
                );
            }
        });

        static::updated(function (Model $model) {
            if (auth()->check()) {
                AuditoriaService::registrar(
                    auth()->user(),
                    'editar',
                    $model,
                );
            }
        });

        static::deleted(function (Model $model) {
            if (auth()->check()) {
                AuditoriaService::registrar(
                    auth()->user(),
                    'eliminar',
                    $model,
                );
            }
        });
    }
}
