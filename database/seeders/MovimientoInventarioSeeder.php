<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MovimientoInventario;
use App\Models\Compra;
use App\Models\Comanda;
use App\Models\MateriaPrima;

class MovimientoInventarioSeeder extends Seeder
{
    public function run(): void
    {
        $compras = Compra::all();
        $comandasCerradas = Comanda::where('estado_comanda', 'cerrada')->get()->take(5);

        // Entradas por compras
        foreach ($compras as $compra) {
            $detalles = $compra->compraDetalles;
            foreach ($detalles as $detalle) {
                MovimientoInventario::create([
                    'compra_id'           => $compra->id,
                    'comanda_id'          => null,
                    'materia_prima_id'    => $detalle->materia_prima_id,
                    'costo_unitario'      => $detalle->costo_unitario,
                    'cantidad_movimiento' => $detalle->cantidad,
                    'tipo_movimiento'     => 'entrada',
                    'nota'                => "Compra #{$compra->id}",
                ]);
            }
        }

        // Salidas por comandas cerradas
        foreach ($comandasCerradas as $comanda) {
            MovimientoInventario::create([
                'compra_id'           => null,
                'comanda_id'          => $comanda->id,
                'materia_prima_id'    => rand(1, 5),
                'costo_unitario'      => round(rand(20, 100) / 100, 2),
                'cantidad_movimiento' => round(rand(5, 30) / 10, 2),
                'tipo_movimiento'     => 'salida',
                'nota'                => "Comanda #{$comanda->numero_correlativo_diario}",
            ]);
        }

        // 2 mermas
        MovimientoInventario::create([
            'compra_id'           => null,
            'comanda_id'          => null,
            'materia_prima_id'    => 3,
            'costo_unitario'      => 2.00,
            'cantidad_movimiento' => 1.50,
            'tipo_movimiento'     => 'merma',
            'nota'                => 'Papas quemadas en fritura',
        ]);

        MovimientoInventario::create([
            'compra_id'           => null,
            'comanda_id'          => null,
            'materia_prima_id'    => 1,
            'costo_unitario'      => 4.50,
            'cantidad_movimiento' => 0.80,
            'tipo_movimiento'     => 'merma',
            'nota'                => 'Carne sobrante del día',
        ]);
    }
}
