<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['nombre' => 'Administrador', 'descripcion' => 'Acceso completo al sistema']);
        Role::create(['nombre' => 'Recepcionista', 'descripcion' => 'Gestión de comandas y clientes']);
        Role::create(['nombre' => 'Cocinero', 'descripcion' => 'Acceso a cocina y catálogo']);
    }
}
