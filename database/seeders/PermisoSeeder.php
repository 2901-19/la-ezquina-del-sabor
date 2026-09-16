<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Comandas
            ['codigo' => 'crear_comanda', 'descripcion' => 'Crear comanda'],
            ['codigo' => 'editar_comanda', 'descripcion' => 'Editar comanda'],
            ['codigo' => 'eliminar_comanda', 'descripcion' => 'Eliminar comanda'],
            ['codigo' => 'imprimir_comanda', 'descripcion' => 'Imprimir comanda'],
            ['codigo' => 'marcar_entrega', 'descripcion' => 'Marcar como entregado'],
            ['codigo' => 'cobrar', 'descripcion' => 'Procesar cobro'],

            // Catálogo
            ['codigo' => 'ver_catalogo', 'descripcion' => 'Ver catálogo'],
            ['codigo' => 'editar_catalogo', 'descripcion' => 'Editar catálogo'],
            ['codigo' => 'eliminar_catalogo', 'descripcion' => 'Eliminar catálogo'],
            ['codigo' => 'editar_recetas', 'descripcion' => 'Gestionar recetas'],

            // Inventario
            ['codigo' => 'ver_inventario', 'descripcion' => 'Ver inventario'],
            ['codigo' => 'editar_inventario', 'descripcion' => 'Gestionar inventario'],
            ['codigo' => 'comprar', 'descripcion' => 'Registrar compras'],
            ['codigo' => 'registrar_merma', 'descripcion' => 'Registrar mermas'],

            // Clientes
            ['codigo' => 'ver_clientes', 'descripcion' => 'Ver clientes'],
            ['codigo' => 'editar_clientes', 'descripcion' => 'Editar clientes'],
            ['codigo' => 'ver_canjes', 'descripcion' => 'Ver canjes de puntos'],

            // Reportes
            ['codigo' => 'ver_reportes', 'descripcion' => 'Ver reportes'],
            ['codigo' => 'exportar_reportes', 'descripcion' => 'Exportar reportes'],

            // Créditos
            ['codigo' => 'gestionar_creditos', 'descripcion' => 'Gestionar créditos'],

            // Jornada
            ['codigo' => 'abrir_jornada', 'descripcion' => 'Abrir jornada'],
            ['codigo' => 'cierre_jornada', 'descripcion' => 'Cerrar jornada'],

            // Sistema
            ['codigo' => 'gestionar_usuarios', 'descripcion' => 'Gestionar usuarios'],
            ['codigo' => 'ver_usuarios', 'descripcion' => 'Ver usuarios'],
            ['codigo' => 'configurar', 'descripcion' => 'Configuración del sistema'],
            ['codigo' => 'ver_auditoria', 'descripcion' => 'Ver auditoría'],
        ];

        foreach ($permisos as $p) {
            Permiso::create($p);
        }
    }
}
