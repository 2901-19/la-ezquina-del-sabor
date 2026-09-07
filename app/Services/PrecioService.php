<?php

namespace App\Services;

use App\Models\Producto;

class PrecioService
{
    public function calcularPrecioMargen(float $costo, float $margen): float
    {
        return $costo + ($costo * ($margen / 100));
    }

    public function calcularPrecioBs(float $precioUsd, float $tasaBcv): float
    {
        return $precioUsd * $tasaBcv;
    }

    public function getPrecio(Producto $producto, float $tasaBcv): array
    {
        if ($producto->tipo_precio === 'margen') {
            $precioUsd = $this->calcularPrecioMargen($producto->precio_usd, $producto->margen_ganancia);
        } else {
            $precioUsd = $producto->precio_usd;
        }

        return [
            'precio_usd' => round($precioUsd, 2),
            'precio_bs' => round($this->calcularPrecioBs($precioUsd, $tasaBcv), 2),
        ];
    }
}
