<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MateriaPrima extends Model
{
    protected $table = 'materias_primas';
    protected $fillable = ['nombre', 'unidad_medida', 'stock_actual', 'stock_minimo', 'costo_unitario_usd', 'ultima_actualizacion'];
    protected $casts = ['ultima_actualizacion' => 'date'];

    public function recetaDetalles(): HasMany
    {
        return $this->hasMany(RecetaDetalle::class);
    }

    public function compraDetalles(): HasMany
    {
        return $this->hasMany(CompraDetalle::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
