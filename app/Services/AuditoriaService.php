<?php

namespace App\Services;

use App\Models\Auditoria;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class AuditoriaService
{
    public static function registrar(
        Usuario $usuario,
        string $accion,
        Model $entidad,
        ?array $datosExtra = null,
    ): Auditoria {
        $datos = $entidad->getChanges();

        if ($datosExtra) {
            $datos = array_merge($datos, $datosExtra);
        }

        return Auditoria::create([
            'usuario_id' => $usuario->id,
            'accion' => $accion,
            'entidad' => class_basename($entidad),
            'entidad_id' => $entidad->getKey(),
            'datos_json' => $datos ?: null,
            'fecha' => now(),
        ]);
    }
}
