<?php

namespace Tests\Feature\Sistema;

use App\Models\Permiso;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioCrudTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['nombre' => 'Admin', 'descripcion' => 'Admin']);
        $permisoVer = Permiso::create(['codigo' => 'ver_usuarios', 'descripcion' => 'Ver usuarios']);
        $permisoEditar = Permiso::create(['codigo' => 'gestionar_usuarios', 'descripcion' => 'Gestionar usuarios']);
        $role->permisos()->sync([$permisoVer->id, $permisoEditar->id]);

        $this->user = Usuario::create([
            'rol_id' => $role->id,
            'username' => 'admin',
            'password_hash' => bcrypt('1234'),
            'nombre_completo' => 'Admin',
            'activo' => true,
        ]);
    }

    public function test_data_filtra_por_rol(): void
    {
        $admin = Role::create(['nombre' => 'Administrador', 'descripcion' => 'Administración']);
        $cocinero = Role::create(['nombre' => 'Cocinero', 'descripcion' => 'Cocina']);

        Usuario::create(['rol_id' => $admin->id, 'username' => 'jefe', 'password_hash' => bcrypt('1234'), 'nombre_completo' => 'Jefe', 'activo' => true]);
        Usuario::create(['rol_id' => $cocinero->id, 'username' => 'chef', 'password_hash' => bcrypt('1234'), 'nombre_completo' => 'Chef', 'activo' => true]);

        $response = $this->actingAs($this->user)->getJson('/sistema/usuarios/data?columns[1][data]=rol.nombre&columns[1][name]=rol_id&columns[1][search][value]=Cocinero');

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre_completo' => 'Chef'])
            ->assertJsonMissing(['nombre_completo' => 'Jefe']);
    }

    public function test_data_filtra_por_estado(): void
    {
        Usuario::create(['rol_id' => $this->user->rol_id, 'username' => 'activo1', 'password_hash' => bcrypt('1234'), 'nombre_completo' => 'Activo Uno', 'activo' => true]);
        Usuario::create(['rol_id' => $this->user->rol_id, 'username' => 'inactivo1', 'password_hash' => bcrypt('1234'), 'nombre_completo' => 'Inactivo Uno', 'activo' => false]);

        $response = $this->actingAs($this->user)->getJson('/sistema/usuarios/data?columns[2][data]=activo&columns[2][name]=activo&columns[2][search][value]=0');

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre_completo' => 'Inactivo Uno'])
            ->assertJsonMissing(['nombre_completo' => 'Activo Uno']);
    }
}
