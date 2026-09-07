<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\HistorialPunto;

class HistorialPuntoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();

        $registros = [
            ['cliente_idx' => 0, 'puntos' => 55,  'tipo' => 'acumulacion'],
            ['cliente_idx' => 0, 'puntos' => 95,  'tipo' => 'acumulacion'],
            ['cliente_idx' => 1, 'puntos' => 85,  'tipo' => 'acumulacion'],
            ['cliente_idx' => 2, 'puntos' => 120, 'tipo' => 'acumulacion'],
            ['cliente_idx' => 2, 'puntos' => -80, 'tipo' => 'canje'],
            ['cliente_idx' => 3, 'puntos' => 45,  'tipo' => 'acumulacion'],
            ['cliente_idx' => 4, 'puntos' => 70,  'tipo' => 'acumulacion'],
            ['cliente_idx' => 4, 'puntos' => 50,  'tipo' => 'acumulacion'],
            ['cliente_idx' => 7, 'puntos' => 100, 'tipo' => 'acumulacion'],
            ['cliente_idx' => 7, 'puntos' => -20, 'tipo' => 'canje'],
        ];

        foreach ($registros as $r) {
            HistorialPunto::create([
                'cliente_id'       => $clientes[$r['cliente_idx']]->id,
                'comanda_id'       => null,
                'puntos_variacion' => $r['puntos'],
                'tipo_operacion'   => $r['tipo'],
            ]);
        }
    }
}
