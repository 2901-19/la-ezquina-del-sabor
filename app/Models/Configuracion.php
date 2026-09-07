<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    protected $fillable = ['clave', 'valor'];

    public static function obtener(string $clave, $default = null)
    {
        $config = static::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    public static function guardar(string $clave, $valor)
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
}
