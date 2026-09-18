<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Receta;
use App\Models\RecetaDetalle;

class RecetaCostoService
{
    private static array $procesadas = [];

    private static int $profundidad = 0;

    public function recalcularPorMateriaPrima(int $materiaPrimaId): void
    {
        $this->iniciar();

        try {
            RecetaDetalle::where('materia_prima_id', $materiaPrimaId)
                ->whereNotNull('receta_id')
                ->distinct()
                ->pluck('receta_id')
                ->each(fn ($id) => $this->recalcularCadena((int) $id));
        } finally {
            $this->finalizar();
        }
    }

    public function recalcularPorSubReceta(int $recetaBaseId): void
    {
        $this->iniciar();

        try {
            RecetaDetalle::where('receta_base_id', $recetaBaseId)
                ->whereNotNull('receta_id')
                ->distinct()
                ->pluck('receta_id')
                ->each(fn ($id) => $this->recalcularCadena((int) $id));
        } finally {
            $this->finalizar();
        }
    }

    public function actualizarProductosDeReceta(int $recetaId): void
    {
        $receta = Receta::find($recetaId);

        if ($receta) {
            $this->actualizarProductosVinculados($receta);
        }
    }

    private function recalcularCadena(int $recetaId): void
    {
        if (isset(self::$procesadas[$recetaId])) {
            return;
        }

        self::$procesadas[$recetaId] = true;

        $receta = Receta::find($recetaId);
        if (! $receta) {
            return;
        }

        $receta->recalcularCosto();

        $this->actualizarProductosVinculados($receta);

        RecetaDetalle::where('receta_base_id', $recetaId)
            ->whereNotNull('receta_id')
            ->distinct()
            ->pluck('receta_id')
            ->each(fn ($padreId) => $this->recalcularCadena((int) $padreId));
    }

    private function actualizarProductosVinculados(Receta $receta): void
    {
        Producto::where('receta_id', $receta->id)
            ->where('tipo_precio', 'margen')
            ->where('indexar_costo_receta', true)
            ->get()
            ->each(function ($producto) use ($receta) {
                $producto->update([
                    'costo_usd' => $receta->costo_total_usd,
                    'precio_usd' => app(PrecioService::class)
                        ->calcularPrecioMargen((float) $receta->costo_total_usd, (float) $producto->margen_ganancia),
                ]);
            });
    }

    private function iniciar(): void
    {
        self::$profundidad++;

        if (self::$profundidad === 1) {
            self::$procesadas = [];
        }
    }

    private function finalizar(): void
    {
        self::$profundidad--;
    }
}
