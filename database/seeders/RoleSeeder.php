<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['nombre' => 'Administrador', 'descripcion' => 'Acceso completo al sistema']);
        Role::create(['nombre' => 'Recepcionista', 'descripcion' => 'Gestión de comandas y clientes']);
        Role::create(['nombre' => 'Cocinero', 'descripcion' => 'Acceso a cocina y catálogo']);
    }
}
