<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nombre', 'Administrador')->first();
        $recepcionistaRole = Role::where('nombre', 'Recepcionista')->first();
        $cocineroRole = Role::where('nombre', 'Cocinero')->first();

        Usuario::create([
            'rol_id' => $adminRole->id,
            'username' => 'admin',
            'password_hash' => bcrypt('1234'),
            'nombre_completo' => 'Carlos Mendoza',
            'activo' => true,
        ]);

        Usuario::create([
            'rol_id' => $recepcionistaRole->id,
            'username' => 'recepcion',
            'password_hash' => bcrypt('1234'),
            'nombre_completo' => 'María López',
            'activo' => true,
        ]);

        Usuario::create([
            'rol_id' => $cocineroRole->id,
            'username' => 'cocina',
            'password_hash' => bcrypt('1234'),
            'nombre_completo' => 'Pedro Sánchez',
            'activo' => true,
        ]);
    }
}
