<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermisoRolSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('nombre', 'Administrador')->first();
        $recepcion = Role::where('nombre', 'Recepcionista')->first();
        $cocina = Role::where('nombre', 'Cocinero')->first();

        // Administrador: todos los permisos
        $todosLosPermisos = Permiso::pluck('id')->toArray();
        $admin->permisos()->sync($todosLosPermisos);

        // Recepcionista: comandas, clientes, créditos, jornada
        $recepcionPermisos = Permiso::whereIn('codigo', [
            'crear_comanda',
            'editar_comanda',
            'imprimir_comanda',
            'marcar_entrega',
            'cobrar',
            'ver_catalogo',
            'ver_clientes',
            'editar_clientes',
            'ver_canjes',
            'gestionar_creditos',
            'abrir_jornada',
            'cierre_jornada',
        ])->pluck('id')->toArray();
        $recepcion->permisos()->sync($recepcionPermisos);

        // Cocinero: catálogo, inventario, entrega
        $cocinaPermisos = Permiso::whereIn('codigo', [
            'ver_catalogo',
            'editar_catalogo',
            'editar_recetas',
            'ver_inventario',
            'editar_inventario',
            'marcar_entrega',
            'ver_clientes',
        ])->pluck('id')->toArray();
        $cocina->permisos()->sync($cocinaPermisos);
    }
}
