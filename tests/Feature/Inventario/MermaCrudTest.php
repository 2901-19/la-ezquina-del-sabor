<?php

namespace Tests\Feature\Inventario;

use App\Models\MateriaPrima;
use App\Models\MovimientoInventario;
use App\Models\Permiso;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MermaCrudTest extends TestCase
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
            'costo_unitario_usd' => 1.5,
        ]);
    }

    public function test_registrar_merma_decrementa_stock(): void
    {
        $mp = $this->crearMateriaPrima();

        $response = $this->actingAs($this->user)->postJson('/inventario/mermas', [
            'materia_prima_id' => $mp->id,
            'cantidad' => 3,
            'motivo' => 'desperdicio',
            'notas' => 'Se cayó',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('materias_primas', ['id' => $mp->id, 'stock_actual' => 7]);
        $this->assertDatabaseHas('movimientos_inventario', ['materia_prima_id' => $mp->id, 'tipo_movimiento' => 'merma', 'cantidad_movimiento' => 3]);
    }

    public function test_merma_rechazada_sin_stock_suficiente(): void
    {
        $mp = $this->crearMateriaPrima();

        $response = $this->actingAs($this->user)->postJson('/inventario/mermas', [
            'materia_prima_id' => $mp->id,
            'cantidad' => 50,
            'motivo' => 'deterioro',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('cantidad');

        $this->assertDatabaseCount('movimientos_inventario', 0);
        $this->assertDatabaseHas('materias_primas', ['id' => $mp->id, 'stock_actual' => 10]);
    }

    public function test_validar_merma_motivo(): void
    {
        $mp = $this->crearMateriaPrima();

        $response = $this->actingAs($this->user)->postJson('/inventario/mermas', [
            'materia_prima_id' => $mp->id,
            'cantidad' => 1,
            'motivo' => 'invalido',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('motivo');
    }

    public function test_mostrar_detalle_merma(): void
    {
        $mp = $this->crearMateriaPrima();

        $mov = MovimientoInventario::create([
            'materia_prima_id' => $mp->id,
            'fecha_movimiento' => now(),
            'costo_unitario' => 1.5,
            'cantidad_movimiento' => 2,
            'tipo_movimiento' => 'merma',
            'nota' => 'Desperdicio. Lote vencido',
        ]);

        $response = $this->actingAs($this->user)->getJson('/inventario/mermas/'.$mov->id);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.materia_prima.nombre', 'Harina');
    }

    public function test_listar_mermas_e_historico(): void
    {
        $mp = $this->crearMateriaPrima();

        MovimientoInventario::create([
            'materia_prima_id' => $mp->id,
            'fecha_movimiento' => now(),
            'costo_unitario' => 1.5,
            'cantidad_movimiento' => 2,
            'tipo_movimiento' => 'merma',
            'nota' => 'Desperdicio',
        ]);

        $response = $this->actingAs($this->user)->getJson('/inventario/mermas/data');

        $response->assertStatus(200)
            ->assertJsonFragment(['materia_prima_nombre' => 'Harina']);

        $movimientos = $this->actingAs($this->user)->getJson('/inventario/movimientos/data');

        $movimientos->assertStatus(200)
            ->assertJsonFragment(['materia_prima' => 'Harina']);
    }
}
