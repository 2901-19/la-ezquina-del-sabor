<?php

namespace Tests\Feature\Catalogo;

use App\Models\Categoria;
use App\Models\Permiso;
use App\Models\Producto;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoCrudTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['nombre' => 'Admin', 'descripcion' => 'Admin']);
        $permisoVer = Permiso::create(['codigo' => 'ver_catalogo', 'descripcion' => 'Ver catálogo']);
        $permisoEditar = Permiso::create(['codigo' => 'editar_catalogo', 'descripcion' => 'Editar catálogo']);
        $role->permisos()->sync([$permisoVer->id, $permisoEditar->id]);

        $this->user = Usuario::create([
            'rol_id' => $role->id,
            'username' => 'admin',
            'password_hash' => bcrypt('1234'),
            'nombre_completo' => 'Admin',
            'activo' => true,
        ]);
    }

    public function test_data_filtra_por_categoria(): void
    {
        $bebidas = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);
        $comidas = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        Producto::create(['categoria_id' => $bebidas->id, 'nombre' => 'Jugo Natural', 'tipo_precio' => 'definido', 'precio_usd' => 2.00, 'es_combo' => false, 'activo' => true]);
        Producto::create(['categoria_id' => $comidas->id, 'nombre' => 'Hamburguesa', 'tipo_precio' => 'definido', 'precio_usd' => 5.00, 'es_combo' => false, 'activo' => true]);

        $response = $this->actingAs($this->user)->getJson('/catalogo/productos/data?columns[1][data]=categoria.nombre&columns[1][name]=categoria_id&columns[1][search][value]=Bebidas');

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => 'Jugo Natural'])
            ->assertJsonMissing(['nombre' => 'Hamburguesa']);
    }

    public function test_data_filtra_por_estado(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Hamburguesa', 'tipo_precio' => 'definido', 'precio_usd' => 5.00, 'es_combo' => false, 'activo' => true]);
        Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Perro Caliente', 'tipo_precio' => 'definido', 'precio_usd' => 4.00, 'es_combo' => false, 'activo' => false]);

        $response = $this->actingAs($this->user)->getJson('/catalogo/productos/data?columns[5][data]=activo&columns[5][name]=activo&columns[5][search][value]=Inactivo');

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => 'Perro Caliente'])
            ->assertJsonMissing(['nombre' => 'Hamburguesa']);
    }
}
