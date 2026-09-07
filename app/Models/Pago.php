<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $fillable = ['comanda_id', 'tarjeta_regalo_id', 'metodo_pago', 'monto_usd', 'monto_ve', 'fecha_pago'];
    protected $casts = ['fecha_pago' => 'datetime'];

    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }

    public function tarjetaRegalo()
    {
        return $this->belongsTo(TarjetaRegalo::class);
    }
}
