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
}
