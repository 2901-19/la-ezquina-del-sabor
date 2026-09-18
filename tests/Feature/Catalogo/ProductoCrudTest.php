<?php

namespace Tests\Feature\Catalogo;

use App\Models\Categoria;
use App\Models\Comanda;
use App\Models\ComandaDetalle;
use App\Models\ComboDetalle;
use App\Models\Jornada;
use App\Models\MateriaPrima;
use App\Models\Permiso;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\RecetaDetalle;
use App\Models\Role;
use App\Models\Usuario;
use App\Services\PrecioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_listar_productos(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);
        Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Jugo Natural', 'tipo_precio' => 'definido', 'precio_usd' => 1.75, 'es_combo' => false, 'activo' => true]);

        $response = $this->actingAs($this->user)->get('/catalogo/productos');

        $response->assertStatus(200);
    }

    public function test_crear_producto_margen_calcula_precio(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Hamburguesa',
            'tipo_precio' => 'margen',
            'costo_usd' => 4.00,
            'margen_ganancia' => 35,
            'es_combo' => false,
            'activo' => true,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('productos', ['nombre' => 'Hamburguesa', 'costo_usd' => 4.00, 'margen_ganancia' => 35, 'precio_usd' => 5.40]);
    }

    public function test_crear_producto_definido(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Refresco 500ml',
            'tipo_precio' => 'definido',
            'precio_usd' => 1.25,
            'es_combo' => false,
            'activo' => true,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('productos', ['nombre' => 'Refresco 500ml', 'costo_usd' => null, 'margen_ganancia' => null, 'precio_usd' => 1.25]);
    }

    public function test_crear_producto_activo_checkbox_web(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Jugo de Naranja',
            'tipo_precio' => 'definido',
            'precio_usd' => 2.00,
            'es_combo' => false,
            'activo' => '1',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('productos', ['nombre' => 'Jugo de Naranja', 'activo' => true]);
    }

    public function test_crear_producto_sin_checkbox_activo(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Jugo de Mora',
            'tipo_precio' => 'definido',
            'precio_usd' => 2.20,
            'es_combo' => false,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('productos', ['nombre' => 'Jugo de Mora', 'activo' => true]);
    }

    public function test_validar_nombre_duplicado(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Hamburguesa', 'tipo_precio' => 'definido', 'precio_usd' => 5.00, 'es_combo' => false, 'activo' => true]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Hamburguesa',
            'tipo_precio' => 'definido',
            'precio_usd' => 6.00,
            'es_combo' => false,
            'activo' => true,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('nombre');
    }

    public function test_validar_margen_sin_costo(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);

        $response = $this->actingAs($this->user)->postJson('/catalogo/productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Hamburguesa',
            'tipo_precio' => 'margen',
            'margen_ganancia' => 35,
            'es_combo' => false,
            'activo' => true,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('costo_usd');
    }

    public function test_editar_producto(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Hamburguesa', 'tipo_precio' => 'margen', 'costo_usd' => 4.00, 'margen_ganancia' => 35, 'precio_usd' => 5.40, 'es_combo' => false, 'activo' => true]);

        $response = $this->actingAs($this->user)->putJson('/catalogo/productos/'.$producto->id, [
            'categoria_id' => $categoria->id,
            'nombre' => 'Hamburguesa Especial',
            'tipo_precio' => 'margen',
            'costo_usd' => 5.00,
            'margen_ganancia' => 40,
            'es_combo' => false,
            'activo' => true,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'nombre' => 'Hamburguesa Especial', 'costo_usd' => 5.00, 'precio_usd' => 7.00]);
    }

    public function test_show_incluye_relaciones_y_precio_bs(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Jugo Natural', 'tipo_precio' => 'definido', 'precio_usd' => 1.75, 'es_combo' => false, 'activo' => true]);

        $response = $this->actingAs($this->user)->getJson('/catalogo/productos/'.$producto->id);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.nombre', 'Jugo Natural')
            ->assertJsonPath('data.categoria.nombre', 'Bebidas')
            ->assertJsonPath('data.precio_bs', 1431.50);
    }

    public function test_eliminar_producto_sin_referencias(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Jugo Natural', 'tipo_precio' => 'definido', 'precio_usd' => 1.75, 'es_combo' => false, 'activo' => true]);

        $response = $this->actingAs($this->user)->deleteJson('/catalogo/productos/'.$producto->id);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }

    public function test_eliminar_producto_usado_en_comanda_se_bloquea(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Hamburguesa', 'tipo_precio' => 'definido', 'precio_usd' => 5.00, 'es_combo' => false, 'activo' => true]);
        $jornada = Jornada::create(['fecha_apertura' => now(), 'usuario_apertura_id' => $this->user->id, 'monto_inicial' => 0, 'estado' => 'abierta']);
        $comanda = Comanda::create(['jornada_id' => $jornada->id, 'usuario_id' => $this->user->id, 'tasa_bcv_aplicada' => 818.00, 'numero_correlativo_diario' => 'C-0001', 'estado_comanda' => 'montar', 'total_usd' => 5.00, 'total_ve' => 4090.00]);
        ComandaDetalle::create(['comanda_id' => $comanda->id, 'producto_id' => $producto->id, 'cantidad' => 1, 'precio_unitario_usd' => 5.00, 'tipo_entrega' => 'comer_aqui']);

        $response = $this->actingAs($this->user)->deleteJson('/catalogo/productos/'.$producto->id);

        $response->assertStatus(409)->assertJson(['success' => false]);
        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
    }

    public function test_eliminar_producto_componente_de_combo_se_bloquea(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Papas Fritas', 'tipo_precio' => 'definido', 'precio_usd' => 2.00, 'es_combo' => false, 'activo' => true]);
        $combo = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Combo Esquina', 'tipo_precio' => 'definido', 'precio_usd' => 8.00, 'es_combo' => true, 'activo' => true]);
        ComboDetalle::create(['combo_producto_id' => $combo->id, 'componente_producto_id' => $producto->id, 'cantidad' => 1, 'porcentaje_descuento' => 0]);

        $response = $this->actingAs($this->user)->deleteJson('/catalogo/productos/'.$producto->id);

        $response->assertStatus(409)->assertJson(['success' => false]);
        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
    }

    public function test_permiso_requerido_para_crear(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $roleSinPermiso = Role::create(['nombre' => 'Solo Lectura', 'descripcion' => 'Sin permisos']);
        $permisoVer = Permiso::where('codigo', 'ver_catalogo')->first();
        $roleSinPermiso->permisos()->sync([$permisoVer->id]);
        $sinPermiso = Usuario::create(['rol_id' => $roleSinPermiso->id, 'username' => 'solo.lectura', 'password_hash' => bcrypt('1234'), 'nombre_completo' => 'Solo Lectura', 'activo' => true]);

        $response = $this->actingAs($sinPermiso)->postJson('/catalogo/productos', ['nombre' => 'X', 'categoria_id' => $categoria->id, 'tipo_precio' => 'definido', 'precio_usd' => 1]);

        $response->assertStatus(403);
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

    public function test_data_busqueda_global_filtra_por_nombre(): void
    {
        $bebidas = Categoria::create(['nombre' => 'Bebidas', 'activa' => true]);
        $comidas = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        Producto::create(['categoria_id' => $bebidas->id, 'nombre' => 'Jugo Natural', 'tipo_precio' => 'definido', 'precio_usd' => 2.00, 'es_combo' => false, 'activo' => true]);
        Producto::create(['categoria_id' => $comidas->id, 'nombre' => 'Hamburguesa', 'tipo_precio' => 'definido', 'precio_usd' => 5.00, 'es_combo' => false, 'activo' => true]);

        $col = function (int $i, string $name, string $searchable) {
            return "columns[$i][data]=$name&columns[$i][name]=$name&columns[$i][searchable]=$searchable&columns[$i][orderable]=true&columns[$i][search][value]=&columns[$i][search][regex]=false";
        };

        $params = 'search[value]=Hamburguesa&search[regex]=false&'.implode('&', [
            $col(0, 'nombre', 'true'),
            $col(1, 'categoria_id', 'true'),
            $col(2, 'tipo_precio', 'true'),
            $col(3, 'precio_usd', 'true'),
            $col(4, 'precio_bs', 'false'),
            $col(5, 'activo', 'false'),
            $col(6, 'acciones', 'false'),
        ]);

        $response = $this->actingAs($this->user)->getJson('/catalogo/productos/data?'.$params);

        $response->assertStatus(200)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonFragment(['nombre' => 'Hamburguesa'])
            ->assertJsonMissing(['nombre' => 'Jugo Natural']);
    }

    private function crearRecetaConCosto(float $cantidad, float $costoUnitario): Receta
    {
        $materiaPrima = MateriaPrima::create([
            'nombre' => 'Carne',
            'unidad_medida' => 'kg',
            'stock_actual' => 10,
            'stock_minimo' => 2,
            'costo_unitario_usd' => $costoUnitario,
        ]);

        $receta = Receta::create(['nombre' => 'Mixta Esquina', 'descripcion' => null, 'costo_total_usd' => 0]);
        RecetaDetalle::create([
            'receta_id' => $receta->id,
            'materia_prima_id' => $materiaPrima->id,
            'cantidad_requerida' => $cantidad,
        ]);
        $receta->recalcularCosto();

        return $receta;
    }

    public function test_crear_producto_toggle_usa_costo_de_receta(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $receta = $this->crearRecetaConCosto(2, 2.00);

        $response = $this->actingAs($this->user)->postJson('/catalogo/productos', [
            'categoria_id' => $categoria->id,
            'receta_id' => $receta->id,
            'indexar_costo_receta' => '1',
            'nombre' => 'Hamburguesa',
            'tipo_precio' => 'margen',
            'costo_usd' => 99.00,
            'margen_ganancia' => 35,
            'es_combo' => false,
            'activo' => true,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('productos', ['id' => Producto::where('nombre', 'Hamburguesa')->value('id'), 'costo_usd' => 4.00, 'indexar_costo_receta' => true, 'precio_usd' => 5.40]);
    }

    public function test_crear_producto_toggle_off_usa_costo_manual(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $receta = $this->crearRecetaConCosto(2, 2.00);

        $response = $this->actingAs($this->user)->postJson('/catalogo/productos', [
            'categoria_id' => $categoria->id,
            'receta_id' => $receta->id,
            'indexar_costo_receta' => '0',
            'nombre' => 'Hamburguesa',
            'tipo_precio' => 'margen',
            'costo_usd' => 3.50,
            'margen_ganancia' => 35,
            'es_combo' => false,
            'activo' => true,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('productos', ['id' => Producto::where('nombre', 'Hamburguesa')->value('id'), 'costo_usd' => 3.50, 'indexar_costo_receta' => false, 'precio_usd' => 4.73]);
    }

    public function test_cambiar_cantidad_receta_actualiza_costo_y_precio_producto(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $receta = $this->crearRecetaConCosto(2, 2.00);
        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'receta_id' => $receta->id,
            'indexar_costo_receta' => true,
            'nombre' => 'Hamburguesa',
            'tipo_precio' => 'margen',
            'costo_usd' => 4.00,
            'margen_ganancia' => 35,
            'precio_usd' => 5.40,
            'es_combo' => false,
            'activo' => true,
        ]);

        $detalle = $receta->recetaDetalles()->first();
        $this->actingAs($this->user)->putJson('/catalogo/recetas/'.$receta->id, [
            'nombre' => $receta->nombre,
            'descripcion' => null,
            'detalles' => [
                ['id' => $detalle->id, 'materia_prima_id' => $detalle->materia_prima_id, 'cantidad_requerida' => 3],
            ],
        ])->assertStatus(200);

        $this->assertEquals(6.00, (float) $receta->fresh()->costo_total_usd);
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'costo_usd' => 6.00, 'precio_usd' => 8.10]);
    }

    public function test_cambiar_costo_materia_prima_actualiza_receta_y_producto(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $materiaPrima = MateriaPrima::create(['nombre' => 'Carne', 'unidad_medida' => 'kg', 'stock_actual' => 10, 'stock_minimo' => 2, 'costo_unitario_usd' => 2.00]);
        $receta = Receta::create(['nombre' => 'Mixta Esquina', 'descripcion' => null, 'costo_total_usd' => 0]);
        RecetaDetalle::create(['receta_id' => $receta->id, 'materia_prima_id' => $materiaPrima->id, 'cantidad_requerida' => 2]);
        $receta->recalcularCosto();
        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'receta_id' => $receta->id,
            'indexar_costo_receta' => true,
            'nombre' => 'Hamburguesa',
            'tipo_precio' => 'margen',
            'costo_usd' => 4.00,
            'margen_ganancia' => 35,
            'precio_usd' => 5.40,
            'es_combo' => false,
            'activo' => true,
        ]);

        $materiaPrima->update(['costo_unitario_usd' => 3.00]);

        $this->assertEquals(6.00, (float) $receta->fresh()->costo_total_usd);
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'costo_usd' => 6.00, 'precio_usd' => 8.10]);
    }

    public function test_backfill_indexa_productos_con_receta(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $receta = $this->crearRecetaConCosto(1, 1.00);
        $conReceta = Producto::create(['categoria_id' => $categoria->id, 'receta_id' => $receta->id, 'nombre' => 'Hamburguesa', 'tipo_precio' => 'margen', 'costo_usd' => 1.00, 'margen_ganancia' => 35, 'precio_usd' => 1.35, 'es_combo' => false, 'activo' => true]);
        $sinReceta = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Jugo Natural', 'tipo_precio' => 'definido', 'precio_usd' => 2.00, 'es_combo' => false, 'activo' => true]);

        DB::table('productos')
            ->whereNotNull('receta_id')
            ->update(['indexar_costo_receta' => true]);

        $this->assertDatabaseHas('productos', ['id' => $conReceta->id, 'indexar_costo_receta' => true]);
        $this->assertDatabaseHas('productos', ['id' => $sinReceta->id, 'indexar_costo_receta' => false]);
    }

    public function test_get_precio_margen_no_duplica_margen(): void
    {
        $categoria = Categoria::create(['nombre' => 'Comidas', 'activa' => true]);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Hamburguesa', 'tipo_precio' => 'margen', 'costo_usd' => 4.00, 'margen_ganancia' => 35, 'precio_usd' => 5.40, 'es_combo' => false, 'activo' => true]);

        $resultado = app(PrecioService::class)->getPrecio($producto, 818.00);

        $this->assertEquals(5.40, $resultado['precio_usd']);
        $this->assertEquals(4417.20, $resultado['precio_bs']);
    }
}
