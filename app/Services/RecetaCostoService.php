<?php

namespace App\Services;

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

        RecetaDetalle::where('receta_base_id', $recetaId)
            ->whereNotNull('receta_id')
            ->distinct()
            ->pluck('receta_id')
            ->each(fn ($padreId) => $this->recalcularCadena((int) $padreId));
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
