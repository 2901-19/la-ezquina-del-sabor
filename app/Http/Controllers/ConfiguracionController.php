<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configs = Configuracion::pluck('valor', 'clave')->toArray();

        return view('sistema.configuracion', compact('configs'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'tasa_bcv' => 'required|numeric|min:0',
            'nombre_negocio' => 'required|string|max:255',
            'telefono_negocio' => 'nullable|string|max:50',
            'direccion_negocio' => 'nullable|string|max:255',
            'logo_path' => 'nullable|string|max:255',
            'impresora_activa' => 'required|in:0,1',
            'impresora_nombre' => 'nullable|string|max:100',
            'regla_puntos' => 'required|integer|min:0',
            'puntos_valor_usd' => 'required|numeric|min:0',
            'params_precios' => 'nullable|json',
        ]);

        foreach ($validated as $clave => $valor) {
            Configuracion::guardar($clave, $valor);
        }

        return response()->json([
            'success' => true,
            'message' => 'Configuración actualizada exitosamente.',
        ]);
    }
}
