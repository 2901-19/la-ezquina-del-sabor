<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\MateriaPrima;

class StockService
{
    public function actualizarStock(MateriaPrima $mp, float $cantidad, string $tipo): void
    {
        if ($tipo === 'entrada') {
            $mp->increment('stock_actual', $cantidad);
        } elseif ($tipo === 'salida') {
            $mp->decrement('stock_actual', $cantidad);
        } elseif ($tipo === 'merma') {
            $mp->decrement('stock_actual', $cantidad);
        }
        $mp->update(['ultima_actualizacion' => now()]);
    }

    public function verificarStock(MateriaPrima $mp): array
    {
        $estado = 'optimo';
        if ($mp->stock_actual <= $mp->stock_minimo * 0.5) {
            $estado = 'critico';
        } elseif ($mp->stock_actual <= $mp->stock_minimo) {
            $estado = 'bajo';
        }
        return ['estado' => $estado, 'stock' => $mp->stock_actual];
    }
}
