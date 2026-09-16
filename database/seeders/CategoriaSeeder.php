<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

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
