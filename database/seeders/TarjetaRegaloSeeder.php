<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\TarjetaRegalo;

class TarjetaRegaloSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();

        TarjetaRegalo::create([
            'cliente_id'      => $clientes[0]->id,
            'codigo'          => 'GC-' . strtoupper(bin2hex(random_bytes(4))),
            'valor_usd'       => 25.00,
            'saldo_usd'       => 10.00,
            'estado'          => 'activa',
            'fecha_emision'   => now()->subDays(15),
            'fecha_expiracion'=> now()->addMonths(3),
        ]);

        TarjetaRegalo::create([
            'cliente_id'      => $clientes[2]->id,
            'codigo'          => 'GC-' . strtoupper(bin2hex(random_bytes(4))),
            'valor_usd'       => 15.00,
            'saldo_usd'       => 0.00,
            'estado'          => 'canjeada',
            'fecha_emision'   => now()->subDays(30),
            'fecha_expiracion'=> now()->addMonths(3),
        ]);
    }
}
