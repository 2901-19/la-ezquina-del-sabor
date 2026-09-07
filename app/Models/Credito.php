<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credito extends Model
{
    protected $table = 'creditos';
    protected $fillable = ['comanda_id', 'cliente_id', 'monto_total_usd', 'saldo_pendiente_usd', 'estado', 'fecha_emision'];
    protected $casts = ['fecha_emision' => 'datetime'];

    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function abonos(): HasMany
    {
        return $this->hasMany(AbonoCredito::class);
    }
}
