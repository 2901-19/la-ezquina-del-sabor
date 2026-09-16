<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditoria';

    protected $fillable = [
        'usuario_id',
        'accion',
        'entidad',
        'entidad_id',
        'datos_json',
        'fecha',
    ];

    protected $casts = [
        'datos_json' => 'array',
        'fecha' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
