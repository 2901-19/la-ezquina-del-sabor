<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jornada extends Model
{
    protected $table = 'jornadas';
    protected $fillable = ['usuario_apertura_id', 'usuario_cierre_id', 'fecha_apertura', 'fecha_cierre', 'tasa_bcv_apertura', 'tasa_bcv_cierre', 'estado', 'resumen_cierre_json'];
    protected $casts = ['fecha_apertura' => 'datetime', 'fecha_cierre' => 'datetime', 'estado' => 'string'];

    public function usuarioApertura()
    {
        return $this->belongsTo(Usuario::class, 'usuario_apertura_id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(Usuario::class, 'usuario_cierre_id');
    }

    public function comandas(): HasMany
    {
        return $this->hasMany(Comanda::class);
    }
}
