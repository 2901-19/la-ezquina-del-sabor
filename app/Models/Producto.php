<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = ['categoria_id', 'receta_id', 'indexar_costo_receta', 'nombre', 'tipo_precio', 'costo_usd', 'margen_ganancia', 'precio_usd', 'es_combo', 'activo'];

    protected $casts = ['activo' => 'boolean', 'es_combo' => 'boolean', 'indexar_costo_receta' => 'boolean', 'costo_usd' => 'decimal:2'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function receta()
    {
        return $this->belongsTo(Receta::class);
    }

    public function comandaDetalles(): HasMany
    {
        return $this->hasMany(ComandaDetalle::class);
    }

    public function comboDetalles(): HasMany
    {
        return $this->hasMany(ComboDetalle::class, 'combo_producto_id');
    }

    public function comboComponentes(): HasMany
    {
        return $this->hasMany(ComboDetalle::class, 'componente_producto_id');
    }
}
