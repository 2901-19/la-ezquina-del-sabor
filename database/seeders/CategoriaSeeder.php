<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = ['Hamburguesas', 'Perros calientes', 'Arepas', 'Combos', 'Bebidas', 'Extras'];
        foreach ($categorias as $cat) {
            Categoria::create(['nombre' => $cat, 'activa' => true]);
        }
    }
}
