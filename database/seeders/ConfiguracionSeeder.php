<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Tasa BCV
            'tasa_bcv' => '818.00',

            // Datos del negocio
            'nombre_negocio' => 'La Esquina del Sabor',
            'telefono_negocio' => '0412-1234567',
            'direccion_negocio' => 'Av. Principal, Caracas',
            'logo_path' => 'images/logo.png',

            // Impresora
            'impresora_activa' => '0',
            'impresora_nombre' => '',

            // Regla de puntos
            'regla_puntos' => '10',
            'puntos_valor_usd' => '0.10',

            // Parámetros de precios
            'params_precios' => '{"margen_default": 30}',
        ];

        foreach ($configs as $clave => $valor) {
            Configuracion::create(['clave' => $clave, 'valor' => $valor]);
        }
    }
}
