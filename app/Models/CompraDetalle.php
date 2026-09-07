<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraDetalle extends Model
{
    protected $table = 'compra_detalles';
    protected $fillable = ['compra_id', 'materia_prima_id', 'cantidad', 'costo_total', 'costo_unitario'];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }
}
