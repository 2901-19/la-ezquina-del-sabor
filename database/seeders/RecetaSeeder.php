<?php

namespace Database\Seeders;

use App\Models\Receta;
use Illuminate\Database\Seeder;

class RecetaSeeder extends Seeder
{
    public function run(): void
    {
        $recetas = [
            ['nombre' => 'Receta Hamburguesa Esquina',  'descripcion' => 'Hamburguesa artesanal con carne 150g, queso y pan'],
            ['nombre' => 'Receta Perro Caliente',        'descripcion' => 'Perro caliente con salchicha, pan suave y salsas'],
            ['nombre' => 'Receta Arepa Dominó',          'descripcion' => 'Arepa con frijoles negros y queso blanco'],
            ['nombre' => 'Receta Arepa Reina Pepiada',   'descripcion' => 'Arepa rellena con pollo, aguacate y mayonesa'],
            ['nombre' => 'Receta Papas Fritas',          'descripcion' => 'Porción de papas fritas crujientes'],
            ['nombre' => 'Receta Hamburguesa Especial',  'descripcion' => 'Doble carne, tocineta y queso'],
            ['nombre' => 'Receta Tocineta Extra',        'descripcion' => 'Porción extra de tocineta crocante'],
            ['nombre' => 'Receta Jugo Natural',          'descripcion' => 'Jugo de naranja natural 500ml'],
            ['nombre' => 'Receta Arepa Dominó Especial', 'descripcion' => 'Arepa doble con frijoles y queso extra'],
            ['nombre' => 'Receta Refresco',              'descripcion' => 'Refresco en lata 500ml'],
        ];

        foreach ($recetas as $r) {
            Receta::firstOrCreate(['nombre' => $r['nombre']], ['descripcion' => $r['descripcion']]);
        }
    }
}
