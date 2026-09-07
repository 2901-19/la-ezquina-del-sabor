<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TarjetaRegalo extends Model
{
    protected $table = 'tarjetas_regalo';
    protected $fillable = ['cliente_id', 'codigo', 'valor_usd', 'saldo_usd', 'estado', 'fecha_emision', 'fecha_expiracion'];
    protected $casts = ['fecha_emision' => 'date', 'fecha_expiracion' => 'date'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}
