<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermisoSeeder::class,
            CategoriaSeeder::class,
            ConfiguracionSeeder::class,
            MateriaPrimaSeeder::class,
            RecetaSeeder::class,
            RecetaDetalleSeeder::class,
            UsuarioSeeder::class,
            ClienteSeeder::class,
            ProductoSeeder::class,
            CompraSeeder::class,
            CompraDetalleSeeder::class,
            JornadaSeeder::class,
            ComandaSeeder::class,
            ComandaDetalleSeeder::class,
            PagoSeeder::class,
            CreditoSeeder::class,
            AbonoCreditoSeeder::class,
            MovimientoInventarioSeeder::class,
            HistorialPuntoSeeder::class,
            TarjetaRegaloSeeder::class,
        ]);
    }
}
