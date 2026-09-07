<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comanda;
use App\Models\Producto;
use App\Models\ComandaDetalle;

class ComandaDetalleSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::where('activo', true)->get();
        $comandas = Comanda::all();

        $entregas = ['comer_aqui', 'llevar', 'delivery'];

        foreach ($comandas as $comanda) {
            $numItems = rand(1, 4);
            $totalUsd = 0;

            for ($i = 0; $i < $numItems; $i++) {
                $producto = $productos->random();
                $cantidad = rand(1, 3);
                $precioUnit = $producto->precio_usd;
                $subtotal = $precioUnit * $cantidad;
                $totalUsd += $subtotal;

                ComandaDetalle::create([
                    'comanda_id'         => $comanda->id,
                    'producto_id'        => $producto->id,
                    'cantidad'           => $cantidad,
                    'precio_unitario_usd' => $precioUnit,
                    'tipo_entrega'       => $comanda->tipo_entrega ?? $entregas[array_rand($entregas)],
                    'entregado'          => $comanda->estado_comanda === 'cerrada',
                    'fecha_entrega'      => $comanda->estado_comanda === 'cerrada' ? $comanda->fecha_creacion->copy()->addMinutes(rand(15, 60)) : null,
                    'nota_producto'      => null,
                ]);
            }

            // Update comanda total
            $comanda->update([
                'total_usd' => round($totalUsd, 2),
                'total_ve'  => round($totalUsd * $comanda->tasa_bcv_aplicada, 2),
            ]);
        }
    }
}
