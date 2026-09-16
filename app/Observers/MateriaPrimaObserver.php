<?php

namespace App\Observers;

use App\Models\MateriaPrima;
use App\Services\RecetaCostoService;

class MateriaPrimaObserver
{
    public function updated(MateriaPrima $materiaPrima): void
    {
        if ($materiaPrima->isDirty('costo_unitario_usd')) {
            app(RecetaCostoService::class)->recalcularPorMateriaPrima($materiaPrima->id);
        }
    }
}
