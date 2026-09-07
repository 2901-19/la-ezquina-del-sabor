<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialPunto extends Model
{
    protected $table = 'historial_puntos';
    protected $fillable = ['cliente_id', 'comanda_id', 'puntos_variacion', 'tipo_operacion', 'fecha'];
    protected $casts = ['fecha' => 'datetime'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }
}
