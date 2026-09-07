<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbonoCredito extends Model
{
    protected $table = 'abonos_credito';
    protected $fillable = ['credito_id', 'metodo_pago', 'monto_usd', 'monto_ve', 'tasa_bcv_aplicada', 'fecha_abono'];
    protected $casts = ['fecha_abono' => 'datetime', 'tasa_bcv_aplicada' => 'decimal:2'];

    public function credito()
    {
        return $this->belongsTo(Credito::class);
    }
}
