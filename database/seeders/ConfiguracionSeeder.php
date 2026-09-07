<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        Configuracion::create(['clave' => 'tasa_bcv', 'valor' => '818.00']);
    }
}
