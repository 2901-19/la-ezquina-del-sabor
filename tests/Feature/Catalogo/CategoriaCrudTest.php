<?php

namespace Tests\Feature\Catalogo;

use App\Models\Categoria;
use App\Models\Permiso;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaCrudTest extends TestCase
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

    public function test_listar_categorias(): void
    {
        Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);

        $response = $this->actingAs($this->user)->get('/catalogo/categorias');

        $response->assertStatus(200);
    }

    public function test_crear_categoria(): void
    {
        $response = $this->actingAs($this->user)->post('/catalogo/categorias', [
            'nombre' => 'Postres',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categorias', ['nombre' => 'Postres']);
    }

    public function test_crear_categoria_checkbox_web(): void
    {
        $response = $this->actingAs($this->user)->post('/catalogo/categorias', [
            'nombre' => 'Postres',
            'activa' => '1',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categorias', ['nombre' => 'Postres', 'activa' => true]);
    }

    public function test_editar_categoria(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);

        $response = $this->actingAs($this->user)->put("/catalogo/categorias/{$categoria->id}", [
            'nombre' => 'Bebidas Premium',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categorias', ['id' => $categoria->id, 'nombre' => 'Bebidas Premium']);
    }

    public function test_eliminar_categoria(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);

        $response = $this->actingAs($this->user)->delete("/catalogo/categorias/{$categoria->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }
}
