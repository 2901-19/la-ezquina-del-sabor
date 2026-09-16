<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receta extends Model
{
    protected $table = 'recetas';

    protected $fillable = ['nombre', 'descripcion', 'costo_total_usd'];

    public function recetaDetalles(): HasMany
    {
        return $this->hasMany(RecetaDetalle::class, 'receta_id');
    }

    public function recetaBaseDetalles(): HasMany
    {
        return $this->hasMany(RecetaDetalle::class, 'receta_base_id');
    }

    public function producto()
    {
        return $this->hasOne(Producto::class);
    }

    public function recalcularCosto(): void
    {
        $costo = $this->recetaDetalles()->with(['materiaPrima', 'recetaBase'])->get()->sum(function ($detalle) {
            if ($detalle->materia_prima_id && $detalle->materiaPrima) {
                return $detalle->materiaPrima->costo_unitario_usd * $detalle->cantidad_requerida;
            }
            if ($detalle->receta_base_id && $detalle->recetaBase) {
                return $detalle->recetaBase->costo_total_usd * $detalle->cantidad_requerida;
            }

            return 0;
        });

        $this->update(['costo_total_usd' => round($costo, 2)]);
    }
}
