<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Receta;

class RecetaSeeder extends Seeder
{
    public function run(): void
    {
        $recetas = [
            ['nombre' => 'Receta Hamburguesa Esquina',  'descripcion' => 'Hamburguesa artesanal con carne 150g, queso y pan',     'costo_total_usd' => 2.10],
            ['nombre' => 'Receta Perro Caliente',        'descripcion' => 'Perro caliente con salchicha, pan suave y salsas',    'costo_total_usd' => 1.25],
            ['nombre' => 'Receta Arepa Dominó',          'descripcion' => 'Arepa con frijoles negros y queso blanco',            'costo_total_usd' => 0.90],
            ['nombre' => 'Receta Arepa Reina Pepiada',   'descripcion' => 'Arepa rellena con pollo, aguacate y mayonesa',        'costo_total_usd' => 1.45],
            ['nombre' => 'Receta Papas Fritas',          'descripcion' => 'Porción de papas fritas crujientes',                 'costo_total_usd' => 0.80],
            ['nombre' => 'Receta Hamburguesa Especial',  'descripcion' => 'Doble carne, tocineta y queso',                      'costo_total_usd' => 3.20],
            ['nombre' => 'Receta Tocineta Extra',        'descripcion' => 'Porción extra de tocineta crocante',                 'costo_total_usd' => 1.05],
            ['nombre' => 'Receta Jugo Natural',          'descripcion' => 'Jugo de naranja natural 500ml',                     'costo_total_usd' => 1.00],
            ['nombre' => 'Receta Arepa Dominó Especial', 'descripcion' => 'Arepa doble con frijoles y queso extra',             'costo_total_usd' => 1.30],
            ['nombre' => 'Receta Refresco',              'descripcion' => 'Refresco en lata 500ml',                            'costo_total_usd' => 0.60],
        ];

        foreach ($recetas as $r) {
            Receta::create($r);
        }
    }
}
