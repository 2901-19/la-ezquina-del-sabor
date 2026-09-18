<?php

namespace Tests\Feature\Inventario;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\MateriaPrima;
use App\Models\Permiso;
use App\Models\Receta;
use App\Models\RecetaDetalle;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraCrudTest extends TestCase
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

    private function crearMateriaPrima(): MateriaPrima
    {
        return MateriaPrima::create([
            'nombre' => 'Harina',
            'unidad_medida' => 'kg',
            'stock_actual' => 10,
            'stock_minimo' => 2,
            'costo_unitario_usd' => 1.0,
        ]);
    }

    public function test_registrar_compra_incrementa_stock_y_costo(): void
    {
        $mp = $this->crearMateriaPrima();

        $response = $this->actingAs($this->user)->postJson('/inventario/compras', [
            'materia_prima_id' => $mp->id,
            'cantidad' => 5,
            'costo_unitario_usd' => 2.5,
            'notas' => 'Proveedor X',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('materias_primas', ['id' => $mp->id, 'stock_actual' => 15, 'costo_unitario_usd' => 2.5]);
        $this->assertDatabaseHas('compras', ['total' => 12.5]);
        $this->assertDatabaseHas('compra_detalles', ['materia_prima_id' => $mp->id, 'cantidad' => 5, 'costo_total' => 12.5]);
        $this->assertDatabaseHas('movimientos_inventario', ['materia_prima_id' => $mp->id, 'tipo_movimiento' => 'entrada', 'cantidad_movimiento' => 5]);
    }

    public function test_compra_acepta_fecha_personalizada(): void
    {
        $mp = $this->crearMateriaPrima();

        $response = $this->actingAs($this->user)->postJson('/inventario/compras', [
            'materia_prima_id' => $mp->id,
            'cantidad' => 3,
            'costo_unitario_usd' => 1.2,
            'fecha_compra' => '2026-09-01',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('compras', ['fecha_compra' => '2026-09-01']);
    }

    public function test_compra_recalcula_costo_de_receta_asociada(): void
    {
        $mp = $this->crearMateriaPrima();

        $receta = Receta::create(['nombre' => 'Masa', 'descripcion' => null, 'costo_total_usd' => 1.0]);
        RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $mp->id, 'cantidad_requerida' => 2]);

        $response = $this->actingAs($this->user)->postJson('/inventario/compras', [
            'materia_prima_id' => $mp->id,
            'cantidad' => 5,
            'costo_unitario_usd' => 2.0,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('recetas', ['id' => $receta->id, 'costo_total_usd' => 4.0]);
    }

    public function test_mostrar_detalle_compra(): void
    {
        $mp = $this->crearMateriaPrima();

        $compra = Compra::create(['fecha_compra' => now(), 'total' => 5.0, 'referencia' => null]);
        CompraDetalle::create(['compra_id' => $compra->id, 'materia_prima_id' => $mp->id, 'cantidad' => 2, 'costo_unitario' => 2.5, 'costo_total' => 5.0]);

        $response = $this->actingAs($this->user)->getJson('/inventario/compras/'.$compra->id);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.compra_detalles.0.materia_prima.nombre', 'Harina');
    }

    public function test_validar_compra_sin_materia_prima(): void
    {
        $response = $this->actingAs($this->user)->postJson('/inventario/compras', [
            'cantidad' => 100,
            'costo_unitario_usd' => 2.0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('materia_prima_id');
    }

    public function test_listar_compras(): void
    {
        $mp = $this->crearMateriaPrima();

        $compra = Compra::create(['fecha_compra' => now(), 'total' => 5.0, 'referencia' => null]);
        CompraDetalle::create(['compra_id' => $compra->id, 'materia_prima_id' => $mp->id, 'cantidad' => 2, 'costo_unitario' => 2.5, 'costo_total' => 5.0]);

        $response = $this->actingAs($this->user)->getJson('/inventario/compras/data');

        $response->assertStatus(200)
            ->assertJsonFragment(['total' => '$ 5.00'])
            ->assertJsonFragment(['materia_prima' => 'Harina']);
    }

    public function test_eliminar_compra_borra_detalles(): void
    {
        $mp = $this->crearMateriaPrima();

        $compra = Compra::create(['fecha_compra' => now(), 'total' => 5.0, 'referencia' => null]);
        CompraDetalle::create(['compra_id' => $compra->id, 'materia_prima_id' => $mp->id, 'cantidad' => 2, 'costo_unitario' => 2.5, 'costo_total' => 5.0]);

        $compra->delete();

        $this->assertDatabaseMissing('compra_detalles', ['compra_id' => $compra->id]);
    }
}
