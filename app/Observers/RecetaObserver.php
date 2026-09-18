<?php

namespace App\Observers;

use App\Models\Receta;
use App\Services\RecetaCostoService;

class RecetaObserver
{
    public function updated(Receta $receta): void
    {
        if ($receta->isDirty('costo_total_usd')) {
            app(RecetaCostoService::class)->recalcularPorSubReceta($receta->id);
            app(RecetaCostoService::class)->actualizarProductosDeReceta($receta->id);
        }
    }
}
