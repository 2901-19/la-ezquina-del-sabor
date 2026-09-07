<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    protected $fillable = ['comanda_id', 'materia_prima_id', 'compra_id', 'fecha_movimiento', 'costo_unitario', 'cantidad_movimiento', 'tipo_movimiento', 'nota'];
    protected $casts = ['fecha_movimiento' => 'datetime'];

    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}
