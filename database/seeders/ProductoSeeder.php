<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['categoria_id' => 1, 'receta_id' => 1,  'nombre' => 'Hamburguesa Esquina',     'tipo_precio' => 'margen',   'margen_ganancia' => 35.00, 'precio_usd' => 0,     'activo' => true],
            ['categoria_id' => 2, 'receta_id' => 2,  'nombre' => 'Perro Caliente',          'tipo_precio' => 'margen',   'margen_ganancia' => 30.00, 'precio_usd' => 0,     'activo' => true],
            ['categoria_id' => 3, 'receta_id' => 3,  'nombre' => 'Arepa Dominó',            'tipo_precio' => 'margen',   'margen_ganancia' => 32.00, 'precio_usd' => 0,     'activo' => true],
            ['categoria_id' => 3, 'receta_id' => 4,  'nombre' => 'Arepa Reina Pepiada',     'tipo_precio' => 'margen',   'margen_ganancia' => 28.00, 'precio_usd' => 0,     'activo' => true],
            ['categoria_id' => 6, 'receta_id' => 5,  'nombre' => 'Papas Fritas Extra',      'tipo_precio' => 'margen',   'margen_ganancia' => 25.00, 'precio_usd' => 0,     'activo' => true],
            ['categoria_id' => 5, 'receta_id' => 10, 'nombre' => 'Refresco 500ml',          'tipo_precio' => 'definido', 'margen_ganancia' => null,  'precio_usd' => 1.25,  'activo' => true],
            ['categoria_id' => 5, 'receta_id' => 8,  'nombre' => 'Jugo Natural',            'tipo_precio' => 'definido', 'margen_ganancia' => null,  'precio_usd' => 1.75,  'activo' => true],
            ['categoria_id' => 1, 'receta_id' => 6,  'nombre' => 'Hamburguesa Especial',    'tipo_precio' => 'margen',   'margen_ganancia' => 40.00, 'precio_usd' => 0,     'activo' => true],
            ['categoria_id' => 6, 'receta_id' => 7,  'nombre' => 'Tocineta Extra',          'tipo_precio' => 'definido', 'margen_ganancia' => null,  'precio_usd' => 1.50,  'activo' => true],
            ['categoria_id' => 3, 'receta_id' => 9,  'nombre' => 'Arepa Dominó Especial',   'tipo_precio' => 'margen',   'margen_ganancia' => 30.00, 'precio_usd' => 0,     'activo' => true],
        ];

        foreach ($productos as $p) {
            if ($p['tipo_precio'] === 'margen') {
                $receta = \App\Models\Receta::find($p['receta_id']);
                $costo = $receta->costo_total_usd;
                $p['precio_usd'] = round($costo + ($costo * $p['margen_ganancia'] / 100), 2);
            }
            Producto::create($p);
        }

        // Combo Esquina (id=11)
        $combo = Producto::create([
            'categoria_id' => 4,
            'receta_id'    => null,
            'nombre'       => 'Combo Esquina',
            'tipo_precio'  => 'margen',
            'margen_ganancia' => 10.00,
            'precio_usd'   => 8.00,
            'es_combo'     => true,
            'activo'       => true,
        ]);

        \App\Models\ComboDetalle::create(['combo_producto_id' => $combo->id, 'componente_producto_id' => 1, 'cantidad' => 1, 'porcentaje_descuento' => 0]);
        \App\Models\ComboDetalle::create(['combo_producto_id' => $combo->id, 'componente_producto_id' => 5, 'cantidad' => 1, 'porcentaje_descuento' => 0]);
        \App\Models\ComboDetalle::create(['combo_producto_id' => $combo->id, 'componente_producto_id' => 6, 'cantidad' => 1, 'porcentaje_descuento' => 0]);
    }
}
