<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = ['nombre', 'telefono', 'direccion_delivery', 'puntos_acumulados'];
    protected $casts = ['fecha_registro' => 'date'];

    public function comandas(): HasMany
    {
        return $this->hasMany(Comanda::class);
    }

    public function tarjetasRegalo(): HasMany
    {
        return $this->hasMany(TarjetaRegalo::class);
    }

    public function historialPuntos(): HasMany
    {
        return $this->hasMany(HistorialPunto::class);
    }

    public function creditos(): HasMany
    {
        return $this->hasMany(Credito::class);
    }
}
