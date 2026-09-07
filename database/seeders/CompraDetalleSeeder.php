<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompraDetalle;

class CompraDetalleSeeder extends Seeder
{
    public function run(): void
    {
        // Compra 1: carne + pan
        CompraDetalle::create(['compra_id' => 1, 'materia_prima_id' => 1, 'cantidad' => 10.00, 'costo_unitario' => 4.50, 'costo_total' => 45.00]);
        CompraDetalle::create(['compra_id' => 1, 'materia_prima_id' => 2, 'cantidad' => 75.00, 'costo_unitario' => 0.30, 'costo_total' => 22.50]);

        // Compra 2: papas + aceite
        CompraDetalle::create(['compra_id' => 2, 'materia_prima_id' => 3, 'cantidad' => 15.00, 'costo_unitario' => 2.00, 'costo_total' => 30.00]);
        CompraDetalle::create(['compra_id' => 2, 'materia_prima_id' => 8, 'cantidad' => 5.00,  'costo_unitario' => 3.50, 'costo_total' => 17.50]);

        // Compra 3: queso + tocineta
        CompraDetalle::create(['compra_id' => 3, 'materia_prima_id' => 4, 'cantidad' => 8.00, 'costo_unitario' => 6.00, 'costo_total' => 48.00]);
        CompraDetalle::create(['compra_id' => 3, 'materia_prima_id' => 9, 'cantidad' => 6.00, 'costo_unitario' => 7.00, 'costo_total' => 42.00]);

        // Compra 4: refrescos + jugo
        CompraDetalle::create(['compra_id' => 4, 'materia_prima_id' => 7,  'cantidad' => 40.00, 'costo_unitario' => 0.60, 'costo_total' => 24.00]);
        CompraDetalle::create(['compra_id' => 4, 'materia_prima_id' => 11, 'cantidad' => 6.00,  'costo_unitario' => 2.50, 'costo_total' => 15.00]);
    }
}
