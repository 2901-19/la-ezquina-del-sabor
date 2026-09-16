<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pertenece_a_un_rol(): void
    {
        $role = Role::create(['nombre' => 'Admin', 'descripcion' => 'Administrador']);
        $user = Usuario::create([
            'rol_id' => $role->id,
            'username' => 'admin',
            'password_hash' => bcrypt('1234'),
            'nombre_completo' => 'Admin User',
            'activo' => true,
        ]);

        $this->assertInstanceOf(Role::class, $user->rol);
        $this->assertEquals('Admin', $user->rol->nombre);
    }

    public function test_usuario_no_guarda_password_hash_en_array_visible(): void
    {
        $role = Role::create(['nombre' => 'Admin', 'descripcion' => 'Admin']);
        $user = Usuario::create([
            'rol_id' => $role->id,
            'username' => 'test',
            'password_hash' => bcrypt('secret'),
            'nombre_completo' => 'Test',
            'activo' => true,
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password_hash', $array);
    }

    public function test_get_auth_password_retorna_password_hash(): void
    {
        $role = Role::create(['nombre' => 'Admin', 'descripcion' => 'Admin']);
        $user = Usuario::create([
            'rol_id' => $role->id,
            'username' => 'test',
            'password_hash' => bcrypt('secret'),
            'nombre_completo' => 'Test',
            'activo' => true,
        ]);

        $this->assertEquals($user->password_hash, $user->getAuthPassword());
    }
}
