<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RecetaDetalle;

class RecetaDetalleSeeder extends Seeder
{
    public function run(): void
    {
        // Receta Hamburguesa Esquina (id=1): carne, pan, queso
        RecetaDetalle::create(['receta_id' => 1, 'materia_prima_id' => 1, 'cantidad_requerida' => 0.15]);
        RecetaDetalle::create(['receta_id' => 1, 'materia_prima_id' => 2, 'cantidad_requerida' => 1.00]);
        RecetaDetalle::create(['receta_id' => 1, 'materia_prima_id' => 4, 'cantidad_requerida' => 0.03]);

        // Receta Perro Caliente (id=2): salchicha, pan
        RecetaDetalle::create(['receta_id' => 2, 'materia_prima_id' => 5, 'cantidad_requerida' => 1.00]);
        RecetaDetalle::create(['receta_id' => 2, 'materia_prima_id' => 2, 'cantidad_requerida' => 1.00]);

        // Receta Arepa Dominó (id=3): arepa, frijoles, queso
        RecetaDetalle::create(['receta_id' => 3, 'materia_prima_id' => 6,  'cantidad_requerida' => 1.00]);
        RecetaDetalle::create(['receta_id' => 3, 'materia_prima_id' => 12, 'cantidad_requerida' => 0.10]);
        RecetaDetalle::create(['receta_id' => 3, 'materia_prima_id' => 4,  'cantidad_requerida' => 0.05]);

        // Receta Arepa Reina Pepiada (id=4): arepa, aguacate
        RecetaDetalle::create(['receta_id' => 4, 'materia_prima_id' => 6,  'cantidad_requerida' => 1.00]);
        RecetaDetalle::create(['receta_id' => 4, 'materia_prima_id' => 10, 'cantidad_requerida' => 0.50]);

        // Receta Papas Fritas (id=5): papas, aceite
        RecetaDetalle::create(['receta_id' => 5, 'materia_prima_id' => 3, 'cantidad_requerida' => 0.20]);
        RecetaDetalle::create(['receta_id' => 5, 'materia_prima_id' => 8, 'cantidad_requerida' => 0.05]);

        // Receta Hamburguesa Especial (id=6): carne doble, tocineta, pan, queso
        RecetaDetalle::create(['receta_id' => 6, 'materia_prima_id' => 1, 'cantidad_requerida' => 0.30]);
        RecetaDetalle::create(['receta_id' => 6, 'materia_prima_id' => 9, 'cantidad_requerida' => 0.05]);
        RecetaDetalle::create(['receta_id' => 6, 'materia_prima_id' => 2, 'cantidad_requerida' => 1.00]);
        RecetaDetalle::create(['receta_id' => 6, 'materia_prima_id' => 4, 'cantidad_requerida' => 0.04]);

        // Receta Tocineta Extra (id=7): tocineta
        RecetaDetalle::create(['receta_id' => 7, 'materia_prima_id' => 9, 'cantidad_requerida' => 0.15]);

        // Receta Jugo Natural (id=8): jugo de naranja
        RecetaDetalle::create(['receta_id' => 8, 'materia_prima_id' => 11, 'cantidad_requerida' => 0.50]);

        // Receta Arepa Dominó Especial (id=9): arepa, frijoles, queso extra
        RecetaDetalle::create(['receta_id' => 9, 'materia_prima_id' => 6,  'cantidad_requerida' => 2.00]);
        RecetaDetalle::create(['receta_id' => 9, 'materia_prima_id' => 12, 'cantidad_requerida' => 0.15]);
        RecetaDetalle::create(['receta_id' => 9, 'materia_prima_id' => 4,  'cantidad_requerida' => 0.08]);

        // Receta Refresco (id=10): refresco
        RecetaDetalle::create(['receta_id' => 10, 'materia_prima_id' => 7, 'cantidad_requerida' => 1.00]);
    }
}
