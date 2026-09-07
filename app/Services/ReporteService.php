<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Comanda;

class ReporteService
{
    public function obtenerResumenJornada(int $jornadaId): array
    {
        $jornada = Comanda::where('jornada_id', $jornadaId)->firstOrFail();

        return [
            'total_ventas' => Comanda::where('jornada_id', $jornadaId)
                ->where('estado_comanda', 'cerrada')
                ->sum('total_usd'),
            'total_comandas' => Comanda::where('jornada_id', $jornadaId)->count(),
            'total_pagado' => Comanda::where('jornada_id', $jornadaId)
                ->where('estado_comanda', 'cerrada')
                ->sum('total_usd'),
            'promedio_pedido' => Comanda::where('jornada_id', $jornadaId)
                ->where('estado_comanda', 'cerrada')
                ->avg('total_usd') ?? 0,
        ];
    }

    public function obtenerVentasPeriodo(string $fechaInicio, string $fechaFin): array
    {
        return Comanda::whereBetween('fecha_creacion', [$fechaInicio, $fechaFin])
            ->where('estado_comanda', 'cerrada')
            ->selectRaw('DATE(fecha_creacion) as fecha, SUM(total_usd) as total, COUNT(*) as cantidad')
            ->groupBy('fecha')
            ->get()
            ->toArray();
    }
}
