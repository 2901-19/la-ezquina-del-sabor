<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComandaDetalle extends Model
{
    protected $table = 'comanda_detalles';
    protected $fillable = ['comanda_id', 'producto_id', 'cantidad', 'precio_unitario_usd', 'tipo_entrega', 'entregado', 'fecha_entrega', 'nota_producto'];
    protected $casts = ['entregado' => 'boolean', 'fecha_entrega' => 'datetime'];

    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
