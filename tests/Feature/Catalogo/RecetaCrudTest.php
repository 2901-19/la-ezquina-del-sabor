<?php

namespace Tests\Feature\Catalogo;

use App\Models\MateriaPrima;
use App\Models\Permiso;
use App\Models\Receta;
use App\Models\RecetaDetalle;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecetaCrudTest extends TestCase
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

    public function test_listar_recetas(): void
    {
        Receta::create(['nombre' => 'Hamburguesa', 'descripcion' => 'Clásica']);

        $response = $this->actingAs($this->user)->get('/catalogo/recetas');

        $response->assertStatus(200);
    }

    public function test_data_recetas(): void
    {
        Receta::create(['nombre' => 'Hamburguesa', 'descripcion' => 'Clásica']);

        $response = $this->actingAs($this->user)->getJson('/catalogo/recetas/data');

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => 'Hamburguesa']);
    }

    public function test_crear_receta_sin_ingredientes(): void
    {
        $response = $this->actingAs($this->user)->postJson('/catalogo/recetas', [
            'nombre' => 'Perro Caliente',
            'descripcion' => 'Con salchicha y papas',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('recetas', ['nombre' => 'Perro Caliente']);
    }

    public function test_crear_receta_con_ingredientes(): void
    {
        $mp1 = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);
        $mp2 = MateriaPrima::create(['nombre' => 'Pan', 'unidad_medida' => 'unidad', 'costo_unitario_usd' => 0.50]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/recetas', [
            'nombre' => 'Hamburguesa Completa',
            'detalles' => [
                ['materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15],
                ['materia_prima_id' => $mp2->id, 'cantidad_requerida' => 1],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('recetas', ['nombre' => 'Hamburguesa Completa']);
        $this->assertDatabaseCount('receta_detalles', 2);
    }

    public function test_crear_receta_con_sub_receta(): void
    {
        $base = Receta::create(['nombre' => 'Salsa Especial', 'costo_total_usd' => 0.50]);
        $mp = MateriaPrima::create(['nombre' => 'Pan', 'unidad_medida' => 'unidad', 'costo_unitario_usd' => 0.50]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/recetas', [
            'nombre' => 'Perro con Salsa',
            'detalles' => [
                ['receta_base_id' => $base->id, 'cantidad_requerida' => 0.02],
                ['materia_prima_id' => $mp->id, 'cantidad_requerida' => 1],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseCount('receta_detalles', 2);
    }

    public function test_crear_receta_nombre_duplicado(): void
    {
        Receta::create(['nombre' => 'Hamburguesa']);

        $response = $this->actingAs($this->user)->postJson('/catalogo/recetas', [
            'nombre' => 'Hamburguesa',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre']);
    }

    public function test_detalle_requiere_materia_prima_o_sub_receta(): void
    {
        $response = $this->actingAs($this->user)->postJson('/catalogo/recetas', [
            'nombre' => 'Receta Sin Material',
            'detalles' => [
                ['cantidad_requerida' => 1],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_show_receta(): void
    {
        $mp = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);
        $receta = Receta::create(['nombre' => 'Hamburguesa']);
        RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp->id, 'cantidad_requerida' => 0.15]);

        $response = $this->actingAs($this->user)->getJson("/catalogo/recetas/{$receta->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['nombre' => 'Hamburguesa'],
            ]);

        $data = $response->json('data');
        $this->assertArrayHasKey('receta_detalles', $data);
        $this->assertCount(1, $data['receta_detalles']);
        $this->assertEquals('Carne', $data['receta_detalles'][0]['materia_prima']['nombre']);
    }

    public function test_editar_receta(): void
    {
        $receta = Receta::create(['nombre' => 'Hamburguesa', 'descripcion' => 'Simple']);

        $response = $this->actingAs($this->user)->putJson("/catalogo/recetas/{$receta->id}", [
            'nombre' => 'Hamburguesa Premium',
            'descripcion' => 'Con queso y tocineta',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('recetas', ['id' => $receta->id, 'nombre' => 'Hamburguesa Premium']);
    }

    public function test_editar_receta_reemplazar_ingredientes(): void
    {
        $mp1 = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);
        $mp2 = MateriaPrima::create(['nombre' => 'Pan', 'unidad_medida' => 'unidad', 'costo_unitario_usd' => 0.50]);
        $mp3 = MateriaPrima::create(['nombre' => 'Queso', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 5.00]);

        $receta = Receta::create(['nombre' => 'Hamburguesa']);
        RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15]);

        $response = $this->actingAs($this->user)->putJson("/catalogo/recetas/{$receta->id}", [
            'nombre' => 'Hamburguesa Con Queso',
            'detalles' => [
                ['materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15],
                ['materia_prima_id' => $mp2->id, 'cantidad_requerida' => 1],
                ['materia_prima_id' => $mp3->id, 'cantidad_requerida' => 0.05],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('receta_detalles', 3);
    }

    public function test_update_preserva_ids_de_detalles_existentes(): void
    {
        $mp1 = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);
        $mp2 = MateriaPrima::create(['nombre' => 'Pan', 'unidad_medida' => 'unidad', 'costo_unitario_usd' => 0.50]);

        $receta = Receta::create(['nombre' => 'Hamburguesa']);
        $d1 = RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15]);
        $d2 = RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp2->id, 'cantidad_requerida' => 1]);

        $response = $this->actingAs($this->user)->putJson("/catalogo/recetas/{$receta->id}", [
            'nombre' => 'Hamburguesa',
            'detalles' => [
                ['id' => $d1->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.20],
                ['id' => $d2->id, 'materia_prima_id' => $mp2->id, 'cantidad_requerida' => 1],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('receta_detalles', ['id' => $d1->id, 'cantidad_requerida' => 0.20]);
        $this->assertDatabaseHas('receta_detalles', ['id' => $d2->id, 'cantidad_requerida' => 1]);
        $this->assertDatabaseCount('receta_detalles', 2);
    }

    public function test_update_agrega_nuevos_detalles(): void
    {
        $mp1 = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);
        $mp2 = MateriaPrima::create(['nombre' => 'Pan', 'unidad_medida' => 'unidad', 'costo_unitario_usd' => 0.50]);

        $receta = Receta::create(['nombre' => 'Hamburguesa']);
        $d1 = RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15]);

        $response = $this->actingAs($this->user)->putJson("/catalogo/recetas/{$receta->id}", [
            'nombre' => 'Hamburguesa',
            'detalles' => [
                ['id' => $d1->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15],
                ['materia_prima_id' => $mp2->id, 'cantidad_requerida' => 1],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('receta_detalles', ['id' => $d1->id]);
        $this->assertDatabaseCount('receta_detalles', 2);
    }

    public function test_update_quita_detalles_eliminados(): void
    {
        $mp1 = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);
        $mp2 = MateriaPrima::create(['nombre' => 'Pan', 'unidad_medida' => 'unidad', 'costo_unitario_usd' => 0.50]);

        $receta = Receta::create(['nombre' => 'Hamburguesa']);
        $d1 = RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15]);
        $d2 = RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp2->id, 'cantidad_requerida' => 1]);

        $response = $this->actingAs($this->user)->putJson("/catalogo/recetas/{$receta->id}", [
            'nombre' => 'Hamburguesa',
            'detalles' => [
                ['id' => $d1->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('receta_detalles', ['id' => $d1->id]);
        $this->assertDatabaseMissing('receta_detalles', ['id' => $d2->id]);
        $this->assertDatabaseCount('receta_detalles', 1);
    }

    public function test_update_modifica_cantidad_de_detalle_existente(): void
    {
        $mp1 = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);

        $receta = Receta::create(['nombre' => 'Hamburguesa']);
        $d1 = RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.15]);

        $response = $this->actingAs($this->user)->putJson("/catalogo/recetas/{$receta->id}", [
            'nombre' => 'Hamburguesa',
            'detalles' => [
                ['id' => $d1->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.30],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('receta_detalles', ['id' => $d1->id, 'materia_prima_id' => $mp1->id, 'cantidad_requerida' => 0.30]);
        $this->assertDatabaseCount('receta_detalles', 1);
    }

    public function test_eliminar_receta_cascades_detalles(): void
    {
        $mp = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);
        $receta = Receta::create(['nombre' => 'Hamburguesa']);
        RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp->id, 'cantidad_requerida' => 0.15]);

        $response = $this->actingAs($this->user)->delete("/catalogo/recetas/{$receta->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('recetas', ['id' => $receta->id]);
        $this->assertDatabaseCount('receta_detalles', 0);
    }

    public function test_costo_total_se_calcula_automaticamente(): void
    {
        $mp = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'costo_unitario_usd' => 3.00]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/recetas', [
            'nombre' => 'Hamburguesa',
            'detalles' => [
                ['materia_prima_id' => $mp->id, 'cantidad_requerida' => 0.15],
            ],
        ]);

        $response->assertStatus(200);
        $receta = Receta::where('nombre', 'Hamburguesa')->first();
        $this->assertEquals(0.45, $receta->costo_total_usd);
    }

    public function test_permiso_requerido(): void
    {
        $sinPermisos = Usuario::create([
            'rol_id' => Role::create(['nombre' => 'Cocinero', 'descripcion' => 'Cocinero'])->id,
            'username' => 'cocina_test',
            'password_hash' => bcrypt('1234'),
            'nombre_completo' => 'Cocina',
            'activo' => true,
        ]);

        $response = $this->actingAs($sinPermisos)->postJson('/catalogo/recetas', [
            'nombre' => 'Hamburguesa',
        ]);

        $response->assertStatus(403);
    }
}
