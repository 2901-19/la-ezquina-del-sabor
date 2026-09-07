<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Credito;
use App\Models\AbonoCredito;

class AbonoCreditoSeeder extends Seeder
{
    public function run(): void
    {
        $creditos = Credito::where('estado', 'parcial')->get();
        $metodos = ['efectivo', 'transferencia', 'punto'];

        foreach ($creditos as $credito) {
            $montoAbono = $credito->monto_total_usd * 0.3;
            AbonoCredito::create([
                'credito_id'         => $credito->id,
                'metodo_pago'        => $metodos[array_rand($metodos)],
                'monto_usd'          => round($montoAbono, 2),
                'monto_ve'           => round($montoAbono * 818, 2),
                'tasa_bcv_aplicada'  => 818.00,
            ]);
        }
    }
}
