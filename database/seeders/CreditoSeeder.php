<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comanda;
use App\Models\Cliente;
use App\Models\Credito;

class CreditoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();

        // 3 créditos pendientes de comandas cerradas
        $cerradas = Comanda::where('estado_comanda', 'cerrada')->get()->take(3);

        $i = 0;
        foreach ($cerradas as $comanda) {
            $monto = $comanda->total_usd;
            $abonado = $i === 0 ? $monto * 0.5 : ($i === 1 ? $monto * 0.3 : 0);
            $saldo = $monto - $abonado;

            Credito::create([
                'comanda_id'            => $comanda->id,
                'cliente_id'            => $comanda->cliente_id,
                'monto_total_usd'       => $monto,
                'saldo_pendiente_usd'   => round($saldo, 2),
                'estado'                => $i === 0 ? 'parcial' : ($i === 1 ? 'parcial' : 'pendiente'),
                'fecha_emision'         => $comanda->fecha_creacion,
            ]);

            $i++;
        }
    }
}
