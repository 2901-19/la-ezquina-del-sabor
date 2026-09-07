<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comanda;
use App\Models\Pago;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = ['efectivo', 'transferencia', 'punto', 'tarjeta'];
        $cerradas = Comanda::where('estado_comanda', 'cerrada')->get();

        foreach ($cerradas as $comanda) {
            $metodo = $metodos[array_rand($metodos)];
            Pago::create([
                'comanda_id'    => $comanda->id,
                'metodo_pago'   => $metodo,
                'monto_usd'     => $comanda->total_usd,
                'monto_ve'      => $comanda->total_ve,
                'fecha_pago'    => $comanda->fecha_creacion->copy()->addMinutes(rand(20, 90)),
            ]);
        }
    }
}
