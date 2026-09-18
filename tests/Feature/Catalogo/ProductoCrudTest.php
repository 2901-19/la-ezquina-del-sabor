<?php

namespace Tests\Feature\Catalogo;

use App\Models\Categoria;
use App\Models\Comanda;
use App\Models\ComandaDetalle;
use App\Models\ComboDetalle;
use App\Models\Jornada;
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
}
