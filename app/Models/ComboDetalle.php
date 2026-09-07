<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComboDetalle extends Model
{
    protected $table = 'combo_detalles';
    protected $fillable = ['combo_producto_id', 'componente_producto_id', 'cantidad', 'porcentaje_descuento'];

    public function comboProducto()
    {
        return $this->belongsTo(Producto::class, 'combo_producto_id');
    }

    public function componenteProducto()
    {
        return $this->belongsTo(Producto::class, 'componente_producto_id');
    }
}
