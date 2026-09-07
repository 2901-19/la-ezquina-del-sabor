<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comanda extends Model
{
    protected $table = 'comandas';
    protected $fillable = ['jornada_id', 'cliente_id', 'usuario_id', 'tasa_bcv_aplicada', 'numero_correlativo_diario', 'nombre_cliente_temporal', 'descripcion_cliente', 'telefono_delivery', 'estado_comanda', 'total_usd', 'total_ve', 'notas_generales', 'fecha_creacion'];
    protected $casts = ['fecha_creacion' => 'datetime', 'fecha_creacion' => 'datetime', 'total_usd' => 'decimal:2', 'total_ve' => 'decimal:2', 'tasa_bcv_aplicada' => 'decimal:2'];

    public function jornada()
    {
        return $this->belongsTo(Jornada::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function comandaDetalles(): HasMany
    {
        return $this->hasMany(ComandaDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}
