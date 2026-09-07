<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MateriaPrima;

class MateriaPrimaSeeder extends Seeder
{
    public function run(): void
    {
        $materias = [
            ['nombre' => 'Carne molida',       'unidad_medida' => 'kg',    'stock_actual' => 15.00, 'stock_minimo' => 5.00,  'costo_unitario_usd' => 4.50],
            ['nombre' => 'Pan artesanal',       'unidad_medida' => 'ud',    'stock_actual' => 50.00, 'stock_minimo' => 20.00, 'costo_unitario_usd' => 0.30],
            ['nombre' => 'Papas fritas',        'unidad_medida' => 'kg',    'stock_actual' => 20.00, 'stock_minimo' => 8.00,  'costo_unitario_usd' => 2.00],
            ['nombre' => 'Queso amarillo',      'unidad_medida' => 'kg',    'stock_actual' => 8.00,  'stock_minimo' => 3.00,  'costo_unitario_usd' => 6.00],
            ['nombre' => 'Salchicha',           'unidad_medida' => 'ud',    'stock_actual' => 40.00, 'stock_minimo' => 15.00, 'costo_unitario_usd' => 0.50],
            ['nombre' => 'Arepa',               'unidad_medida' => 'ud',    'stock_actual' => 30.00, 'stock_minimo' => 10.00, 'costo_unitario_usd' => 0.25],
            ['nombre' => 'Refresco 500ml',      'unidad_medida' => 'ud',    'stock_actual' => 48.00, 'stock_minimo' => 20.00, 'costo_unitario_usd' => 0.60],
            ['nombre' => 'Aceite vegetal',      'unidad_medida' => 'lt',    'stock_actual' => 10.00, 'stock_minimo' => 4.00,  'costo_unitario_usd' => 3.50],
            ['nombre' => 'Tocineta',            'unidad_medida' => 'kg',    'stock_actual' => 5.00,  'stock_minimo' => 2.00,  'costo_unitario_usd' => 7.00],
            ['nombre' => 'Aguacate',            'unidad_medida' => 'ud',    'stock_actual' => 12.00, 'stock_minimo' => 5.00,  'costo_unitario_usd' => 1.20],
            ['nombre' => 'Jugo de naranja',     'unidad_medida' => 'lt',    'stock_actual' => 8.00,  'stock_minimo' => 3.00,  'costo_unitario_usd' => 2.50],
            ['nombre' => 'Frijoles negros',     'unidad_medida' => 'kg',    'stock_actual' => 10.00, 'stock_minimo' => 4.00,  'costo_unitario_usd' => 1.80],
        ];

        foreach ($materias as $mp) {
            MateriaPrima::create($mp);
        }
    }
}
