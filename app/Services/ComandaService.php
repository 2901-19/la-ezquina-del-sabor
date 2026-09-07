<?php

namespace App\Services;

use App\Models\Comanda;
use App\Models\ComandaDetalle;
use App\Models\Producto;

class ComandaService
{
    public function crearComanda(array $data): Comanda
    {
        return Comanda::create([
            'jornada_id' => $data['jornada_id'],
            'cliente_id' => $data['cliente_id'] ?? null,
            'usuario_id' => $data['usuario_id'],
            'tasa_bcv_aplicada' => $data['tasa_bcv_aplicada'],
            'numero_correlativo_diario' => $data['numero_correlativo_diario'],
            'nombre_cliente_temporal' => $data['nombre_cliente_temporal'] ?? null,
            'descripcion_cliente' => $data['descripcion_cliente'] ?? null,
            'telefono_delivery' => $data['telefono_delivery'] ?? null,
            'estado_comanda' => 'montar',
            'total_usd' => 0,
            'total_ve' => 0,
            'notas_generales' => $data['notas_generales'] ?? null,
        ]);
    }

    public function agregarProducto(Comanda $comanda, int $productoId, int $cantidad, string $tipoEntrega, ?string $nota): ComandaDetalle
    {
        $producto = Producto::findOrFail($productoId);
        $precioUsd = $producto->precio_usd;

        return ComandaDetalle::create([
            'comanda_id' => $comanda->id,
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'precio_unitario_usd' => $precioUsd,
            'tipo_entrega' => $tipoEntrega,
            'entregado' => false,
            'nota_producto' => $nota,
        ]);
    }

    public function calcularTotales(Comanda $comanda, float $tasaBcv): void
    {
        $totalUsd = $comanda->comandaDetalles()->sum(\DB::raw('cantidad * precio_unitario_usd'));
        $comanda->update([
            'total_usd' => round($totalUsd, 2),
            'total_ve' => round($totalUsd * $tasaBcv, 2),
        ]);
    }
}
