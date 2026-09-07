<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'María González',    'telefono' => '0414-1234567', 'direccion_delivery' => 'Av. Libertador, Caracas',         'puntos_acumulados' => 150],
            ['nombre' => 'Carlos Páez',       'telefono' => '0412-9876543', 'direccion_delivery' => 'Calle 5, Los Teques',            'puntos_acumulados' => 85],
            ['nombre' => 'Ana Ríos',          'telefono' => '0424-5551234', 'direccion_delivery' => 'Urb. El Trapiche, Miranda',     'puntos_acumulados' => 200],
            ['nombre' => 'Luis Fernández',    'telefono' => '0416-7778899', 'direccion_delivery' => null,                             'puntos_acumulados' => 45],
            ['nombre' => 'Jorge Martínez',    'telefono' => '0412-3334455', 'direccion_delivery' => 'Av. Principal, Guarenas',         'puntos_acumulados' => 120],
            ['nombre' => 'Carmen Herrera',    'telefono' => '0424-8889900', 'direccion_delivery' => 'Callejón 3, Baruta',             'puntos_acumulados' => 75],
            ['nombre' => 'Roberto Díaz',      'telefono' => '0416-2223344', 'direccion_delivery' => null,                             'puntos_acumulados' => 30],
            ['nombre' => 'Lisbeth Montilla',  'telefono' => '0414-6667788', 'direccion_delivery' => 'Urb. Las Mercedes, Caracas',    'puntos_acumulados' => 180],
            ['nombre' => 'Francisco Suárez',  'telefono' => '0412-1112233', 'direccion_delivery' => 'Av. Francisco de Miranda',       'puntos_acumulados' => 60],
            ['nombre' => 'Elena Vargas',      'telefono' => '0424-4445566', 'direccion_delivery' => 'Calle Principal, Los Dos Caminos','puntos_acumulados' => 95],
            ['nombre' => 'Miguel Torres',     'telefono' => '0416-9990011', 'direccion_delivery' => null,                             'puntos_acumulados' => 40],
            ['nombre' => 'Patricia Ramos',    'telefono' => '0414-3332211', 'direccion_delivery' => 'Av. Norte, El Hatillo',          'puntos_acumulados' => 110],
        ];

        foreach ($clientes as $c) {
            Cliente::create($c);
        }
    }
}
