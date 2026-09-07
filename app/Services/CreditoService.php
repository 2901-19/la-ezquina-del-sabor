<?php

namespace App\Services;

use App\Models\Credito;

class CreditoService
{
    public function crearCredito(array $data): Credito
    {
        return Credito::create([
            'comanda_id' => $data['comanda_id'],
            'cliente_id' => $data['cliente_id'],
            'monto_total_usd' => $data['monto_total_usd'],
            'saldo_pendiente_usd' => $data['monto_total_usd'],
            'estado' => 'pendiente',
            'fecha_emision' => now(),
        ]);
    }

    public function registrarAbono(Credito $credito, float $montoUsd, float $tasaBcv): void
    {
        $credito->decrement('saldo_pendiente_usd', $montoUsd);
        $credito->update([
            'estado' => $credito->saldo_pendiente_usd <= 0 ? 'pagado' : 'parcial',
        ]);
    }

    public function getEstadoCredito(Credito $credito): string
    {
        if ($credito->saldo_pendiente_usd <= 0) return 'pagado';
        if ($credito->saldo_pendiente_usd < $credito->monto_total_usd) return 'parcial';
        return 'pendiente';
    }
}
