<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecetaDetalle extends Model
{
    protected $table = 'receta_detalles';
    protected $fillable = ['receta_id', 'receta_base_id', 'materia_prima_id', 'cantidad_requerida'];

    public function receta()
    {
        return $this->belongsTo(Receta::class);
    }

    public function recetaBase()
    {
        return $this->belongsTo(Receta::class, 'receta_base_id');
    }

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }
}
