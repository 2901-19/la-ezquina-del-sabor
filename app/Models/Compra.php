<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    protected $table = 'compras';
    protected $fillable = ['fecha_compra', 'total', 'referencia'];
    protected $casts = ['fecha_compra' => 'date'];

    public function compraDetalles(): HasMany
    {
        return $this->hasMany(CompraDetalle::class);
    }
}
