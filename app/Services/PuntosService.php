<?php

namespace App\Services;

use App\Models\Cliente;

class PuntosService
{
    public function acumularPuntos(Cliente $cliente, float $montoUsd): void
    {
        $puntos = (int) round($montoUsd);
        $cliente->increment('puntos_acumulados', $puntos);
    }

    public function canjearPuntos(Cliente $cliente, int $puntos): bool
    {
        if ($cliente->puntos_acumulados >= $puntos) {
            $cliente->decrement('puntos_acumulados', $puntos);
            return true;
        }
        return false;
    }
}
