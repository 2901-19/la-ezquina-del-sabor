<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permiso;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            ['codigo' => 'crear_comanda', 'descripcion' => 'Crear comanda'],
            ['codigo' => 'imprimir_comanda', 'descripcion' => 'Imprimir comanda'],
            ['codigo' => 'marcar_entrega', 'descripcion' => 'Marcar como entregado'],
            ['codigo' => 'cobrar', 'descripcion' => 'Procesar cobro'],
            ['codigo' => 'ver_catalogo', 'descripcion' => 'Ver catálogo'],
            ['codigo' => 'editar_catalogo', 'descripcion' => 'Editar catálogo'],
            ['codigo' => 'editar_recetas', 'descripcion' => 'Gestionar recetas'],
            ['codigo' => 'editar_inventario', 'descripcion' => 'Gestionar inventario'],
            ['codigo' => 'ver_clientes', 'descripcion' => 'Ver clientes'],
            ['codigo' => 'ver_canjes', 'descripcion' => 'Ver canjes de puntos'],
            ['codigo' => 'ver_reportes', 'descripcion' => 'Ver reportes'],
            ['codigo' => 'gestionar_creditos', 'descripcion' => 'Gestionar créditos'],
            ['codigo' => 'cierre_jornada', 'descripcion' => 'Cerrar jornada'],
            ['codigo' => 'gestionar_usuarios', 'descripcion' => 'Gestionar usuarios'],
            ['codigo' => 'configurar', 'descripcion' => 'Configuración del sistema'],
        ];
        foreach ($permisos as $p) {
            Permiso::create($p);
        }
    }
}
