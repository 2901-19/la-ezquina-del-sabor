<?php

namespace App\Services;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Http;

class TasaBcvService
{
    public function getTasaActual(): float
    {
        $tasaDb = Configuracion::obtener('tasa_bcv');
        if ($tasaDb && is_numeric($tasaDb)) {
            return (float) $tasaDb;
        }

        try {
            $response = Http::timeout(10)->get('https://api.dolserasa.com/v1/dollar/latest');
            return $response->json()['data']['price'] ?? 42.50;
        } catch (\Exception $e) {
            return 42.50;
        }
    }

    public function calcularBs(float $usd, float $tasa): float
    {
        return $usd * $tasa;
    }
}
