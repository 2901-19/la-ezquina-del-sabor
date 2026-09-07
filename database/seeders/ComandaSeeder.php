<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jornada;
use App\Models\Comanda;
use App\Models\Cliente;
use App\Models\Usuario;

class ComandaSeeder extends Seeder
{
    public function run(): void
    {
        $jornadas = Jornada::orderBy('id')->get();
        $admin = Usuario::where('username', 'admin')->first();
        $recepcion = Usuario::where('username', 'recepcion')->first();
        $clientes = Cliente::all();

        $estados = ['montar', 'entrega', 'cobrar', 'cerrada'];
        $entregas = ['comer_aqui', 'llevar', 'delivery'];
        $mesas = ['Mesa 1', 'Mesa 2', 'Mesa 3', 'Mesa 4', 'Mesa 5'];

        $num = 1;
        // Distribute 25 orders across 5 jornadas
        $dist = [5, 5, 5, 5, 5]; // 5 per jornada

        foreach ($jornadas as $jIdx => $jornada) {
            $count = $dist[$jIdx];
            for ($i = 0; $i < $count; $i++) {
                $estado = $jornada->estado === 'cerrada' ? 'cerrada' : $estados[array_rand($estados)];
                $tipoEntrega = $entregas[array_rand($entregas)];
                $cliente = $clientes->random();
                $usuario = rand(0, 1) ? $admin : $recepcion;

                $totalUsd = round(rand(300, 1200) / 100, 2);

                $comanda = Comanda::create([
                    'jornada_id'                => $jornada->id,
                    'cliente_id'                => $cliente->id,
                    'usuario_id'                => $usuario->id,
                    'tasa_bcv_aplicada'         => $jornada->tasa_bcv_apertura,
                    'numero_correlativo_diario' => str_pad($num, 3, '0', STR_PAD_LEFT),
                    'nombre_cliente_temporal'   => null,
                    'telefono_delivery'         => $tipoEntrega === 'delivery' ? $cliente->telefono : null,
                    'estado_comanda'            => $estado,
                    'total_usd'                 => $totalUsd,
                    'total_ve'                  => round($totalUsd * $jornada->tasa_bcv_apertura, 2),
                    'notas_generales'           => null,
                    'fecha_creacion'            => $jornada->fecha_apertura->copy()->addMinutes(rand(10, 480)),
                ]);

                $num++;
            }
        }
    }
}
