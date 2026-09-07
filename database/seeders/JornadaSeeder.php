<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jornada;
use App\Models\Usuario;

class JornadaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::where('username', 'admin')->first();
        $recepcion = Usuario::where('username', 'recepcion')->first();

        // 4 jornadas cerradas
        Jornada::create([
            'usuario_apertura_id' => $admin->id,
            'usuario_cierre_id'   => $admin->id,
            'fecha_apertura'      => now()->subDays(4)->setTime(8, 0),
            'fecha_cierre'        => now()->subDays(4)->setTime(17, 0),
            'tasa_bcv_apertura'   => 815.00,
            'tasa_bcv_cierre'     => 815.00,
            'estado'              => 'cerrada',
        ]);

        Jornada::create([
            'usuario_apertura_id' => $recepcion->id,
            'usuario_cierre_id'   => $recepcion->id,
            'fecha_apertura'      => now()->subDays(3)->setTime(8, 0),
            'fecha_cierre'        => now()->subDays(3)->setTime(17, 30),
            'tasa_bcv_apertura'   => 816.00,
            'tasa_bcv_cierre'     => 816.00,
            'estado'              => 'cerrada',
        ]);

        Jornada::create([
            'usuario_apertura_id' => $admin->id,
            'usuario_cierre_id'   => $admin->id,
            'fecha_apertura'      => now()->subDays(2)->setTime(8, 0),
            'fecha_cierre'        => now()->subDays(2)->setTime(16, 45),
            'tasa_bcv_apertura'   => 817.00,
            'tasa_bcv_cierre'     => 817.00,
            'estado'              => 'cerrada',
        ]);

        Jornada::create([
            'usuario_apertura_id' => $recepcion->id,
            'usuario_cierre_id'   => $recepcion->id,
            'fecha_apertura'      => now()->subDay()->setTime(8, 0),
            'fecha_cierre'        => now()->subDay()->setTime(17, 15),
            'tasa_bcv_apertura'   => 818.00,
            'tasa_bcv_cierre'     => 818.00,
            'estado'              => 'cerrada',
        ]);

        // 1 jornada abierta (hoy)
        Jornada::create([
            'usuario_apertura_id' => $admin->id,
            'usuario_cierre_id'   => null,
            'fecha_apertura'      => now()->setTime(8, 0),
            'fecha_cierre'        => null,
            'tasa_bcv_apertura'   => 818.00,
            'tasa_bcv_cierre'     => null,
            'estado'              => 'abierta',
        ]);
    }
}
