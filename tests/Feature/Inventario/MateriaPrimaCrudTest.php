<?php

namespace Tests\Feature\Inventario;

use App\Models\MateriaPrima;
use App\Models\Permiso;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriaPrimaCrudTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['nombre' => 'Admin', 'descripcion' => 'Admin']);
        $permisoVer = Permiso::create(['codigo' => 'ver_inventario', 'descripcion' => 'Ver inventario']);
        $permisoEditar = Permiso::create(['codigo' => 'editar_inventario', 'descripcion' => 'Gestionar inventario']);
        $role->permisos()->sync([$permisoVer->id, $permisoEditar->id]);

        $this->user = Usuario::create([
            'rol_id' => $role->id,
            'username' => 'admin',
            'password_hash' => bcrypt('1234'),
            'nombre_completo' => 'Admin',
            'activo' => true,
        ]);
    }

    public function test_listar_materias_primas(): void
    {
        MateriaPrima::create(['nombre' => 'Harina', 'unidad_medida' => 'kg', 'stock_actual' => 10, 'stock_minimo' => 2, 'costo_unitario_usd' => 1.5]);

        $response = $this->actingAs($this->user)->get('/inventario/materias-primas');

        $response->assertStatus(200);
    }

    public function test_mostrar_detalle_materia_prima(): void
    {
        $mp = MateriaPrima::create(['nombre' => 'Harina', 'unidad_medida' => 'kg', 'stock_actual' => 10, 'stock_minimo' => 2, 'costo_unitario_usd' => 1.5]);

        $response = $this->actingAs($this->user)->getJson('/inventario/materias-primas/'.$mp->id);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonFragment(['nombre' => 'Harina']);
    }

    public function test_crear_materia_prima(): void
    {
        $response = $this->actingAs($this->user)->postJson('/inventario/materias-primas', [
            'nombre' => 'Queso',
            'unidad_medida' => 'g',
            'stock_actual' => 500,
            'stock_minimo' => 100,
            'costo_unitario_usd' => 4.25,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('materias_primas', ['nombre' => 'Queso', 'costo_unitario_usd' => 4.25]);
    }

    public function test_validar_nombre_duplicado_materia_prima(): void
    {
        MateriaPrima::create(['nombre' => 'Harina', 'unidad_medida' => 'kg', 'stock_actual' => 10, 'stock_minimo' => 2, 'costo_unitario_usd' => 1.5]);

        $response = $this->actingAs($this->user)->postJson('/inventario/materias-primas', [
            'nombre' => 'Harina',
            'unidad_medida' => 'kg',
            'stock_actual' => 5,
            'stock_minimo' => 1,
            'costo_unitario_usd' => 2.0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('nombre');
    }

    public function test_editar_materia_prima(): void
    {
        $mp = MateriaPrima::create(['nombre' => 'Harina', 'unidad_medida' => 'kg', 'stock_actual' => 10, 'stock_minimo' => 2, 'costo_unitario_usd' => 1.5]);

        $response = $this->actingAs($this->user)->putJson('/inventario/materias-primas/'.$mp->id, [
            'nombre' => 'Harina de trigo',
            'unidad_medida' => 'kg',
            'stock_actual' => 15,
            'stock_minimo' => 3,
            'costo_unitario_usd' => 1.8,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('materias_primas', ['id' => $mp->id, 'nombre' => 'Harina de trigo', 'costo_unitario_usd' => 1.8]);
    }

    public function test_eliminar_materia_prima(): void
    {
        $mp = MateriaPrima::create(['nombre' => 'Harina', 'unidad_medida' => 'kg', 'stock_actual' => 10, 'stock_minimo' => 2, 'costo_unitario_usd' => 1.5]);

        $response = $this->actingAs($this->user)->deleteJson('/inventario/materias-primas/'.$mp->id);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseMissing('materias_primas', ['id' => $mp->id]);
    }

    public function test_data_incluye_estado_stock(): void
    {
        MateriaPrima::create(['nombre' => 'Critica', 'unidad_medida' => 'kg', 'stock_actual' => 1, 'stock_minimo' => 10, 'costo_unitario_usd' => 1.0]);
        MateriaPrima::create(['nombre' => 'Optima', 'unidad_medida' => 'kg', 'stock_actual' => 100, 'stock_minimo' => 10, 'costo_unitario_usd' => 1.0]);

        $response = $this->actingAs($this->user)->getJson('/inventario/materias-primas/data');

        $response->assertStatus(200)
            ->assertJsonFragment(['estado_stock' => 'critico'])
            ->assertJsonFragment(['estado_stock' => 'optimo']);
    }
}
