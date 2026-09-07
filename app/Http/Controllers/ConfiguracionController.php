<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $tasa = Configuracion::obtener('tasa_bcv', '42.50');
        return view('sistema.configuracion', compact('tasa'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'tasa_bcv' => 'required|numeric|min:0',
        ]);

        Configuracion::guardar('tasa_bcv', $validated['tasa_bcv']);

        return response()->json(['success' => true, 'message' => 'Configuración actualizada exitosamente.', 'tasa_bcv' => $validated['tasa_bcv']]);
    }
}
