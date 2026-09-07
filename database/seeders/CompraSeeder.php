<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Compra;
use App\Models\CompraDetalle;

class CompraSeeder extends Seeder
{
    public function run(): void
    {
        $compras = [
            ['fecha_compra' => now()->subDays(4)->startOfDay(), 'total' => 67.50,  'referencia' => 'Proveedor Los Andes'],
            ['fecha_compra' => now()->subDays(3)->startOfDay(), 'total' => 45.00,  'referencia' => 'Distribuidora Central'],
            ['fecha_compra' => now()->subDays(1)->startOfDay(), 'total' => 92.00,  'referencia' => 'Proveedor Los Andes'],
            ['fecha_compra' => now()->subHours(3),              'total' => 38.50,  'referencia' => 'Abastos Rapid'],
        ];

        foreach ($compras as $c) {
            Compra::create($c);
        }
    }
}
