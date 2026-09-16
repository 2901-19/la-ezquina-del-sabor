<?php

namespace Database\Seeders;

use App\Models\MateriaPrima;
use App\Models\Receta;
use App\Models\RecetaDetalle;
use Illuminate\Database\Seeder;

class RecetaDetalleSeeder extends Seeder
{
    public function run(): void
    {
        $mp = fn (string $nombre) => MateriaPrima::where('nombre', $nombre)->first()?->id;
        $receta = fn (string $nombre) => Receta::where('nombre', $nombre)->first()?->id;

        $detalles = [
            // Receta Hamburguesa Esquina: carne, pan, queso
            ['receta' => 'Receta Hamburguesa Esquina', 'materia' => 'Carne molida',    'cantidad' => 0.15],
            ['receta' => 'Receta Hamburguesa Esquina', 'materia' => 'Pan de hamburguesa', 'cantidad' => 1.00],
            ['receta' => 'Receta Hamburguesa Esquina', 'materia' => 'Queso amarillo',   'cantidad' => 0.03],

            // Receta Perro Caliente: salchicha, pan
            ['receta' => 'Receta Perro Caliente', 'materia' => 'Salchicha',           'cantidad' => 1.00],
            ['receta' => 'Receta Perro Caliente', 'materia' => 'Pan de hamburguesa',   'cantidad' => 1.00],

            // Receta Arepa Dominó: arepa, frijoles, queso
            ['receta' => 'Receta Arepa Dominó', 'materia' => 'Arepa',            'cantidad' => 1.00],
            ['receta' => 'Receta Arepa Dominó', 'materia' => 'Frijoles negros',  'cantidad' => 0.10],
            ['receta' => 'Receta Arepa Dominó', 'materia' => 'Queso amarillo',   'cantidad' => 0.05],

            // Receta Arepa Reina Pepiada: arepa, aguacate
            ['receta' => 'Receta Arepa Reina Pepiada', 'materia' => 'Arepa',       'cantidad' => 1.00],
            ['receta' => 'Receta Arepa Reina Pepiada', 'materia' => 'Aguacate',    'cantidad' => 0.50],

            // Receta Papas Fritas: papas, aceite
            ['receta' => 'Receta Papas Fritas', 'materia' => 'Papas',     'cantidad' => 0.20],
            ['receta' => 'Receta Papas Fritas', 'materia' => 'Aceite',    'cantidad' => 0.05],

            // Receta Hamburguesa Especial: carne doble, tocineta, pan, queso
            ['receta' => 'Receta Hamburguesa Especial', 'materia' => 'Carne molida',    'cantidad' => 0.30],
            ['receta' => 'Receta Hamburguesa Especial', 'materia' => 'Tocineta',        'cantidad' => 0.05],
            ['receta' => 'Receta Hamburguesa Especial', 'materia' => 'Pan de hamburguesa', 'cantidad' => 1.00],
            ['receta' => 'Receta Hamburguesa Especial', 'materia' => 'Queso amarillo',   'cantidad' => 0.04],

            // Receta Tocineta Extra: tocineta
            ['receta' => 'Receta Tocineta Extra', 'materia' => 'Tocineta', 'cantidad' => 0.15],

            // Receta Jugo Natural: jugo de naranja
            ['receta' => 'Receta Jugo Natural', 'materia' => 'Jugo de naranja', 'cantidad' => 0.50],

            // Receta Arepa Dominó Especial: arepa doble, frijoles, queso extra
            ['receta' => 'Receta Arepa Dominó Especial', 'materia' => 'Arepa',           'cantidad' => 2.00],
            ['receta' => 'Receta Arepa Dominó Especial', 'materia' => 'Frijoles negros', 'cantidad' => 0.15],
            ['receta' => 'Receta Arepa Dominó Especial', 'materia' => 'Queso amarillo',  'cantidad' => 0.08],

            // Receta Refresco: refresco
            ['receta' => 'Receta Refresco', 'materia' => 'Refresco', 'cantidad' => 1.00],
        ];

        foreach ($detalles as $d) {
            $recetaId = $receta($d['receta']);
            $materiaId = $mp($d['materia']);

            if ($recetaId && $materiaId) {
                RecetaDetalle::firstOrCreate(
                    ['receta_id' => $recetaId, 'materia_prima_id' => $materiaId],
                    ['cantidad_requerida' => $d['cantidad']]
                );
            }
        }

        Receta::all()->each->recalcularCosto();
    }
}
