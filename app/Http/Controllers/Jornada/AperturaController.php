<?php

namespace App\Http\Controllers\Jornada;

use App\Http\Controllers\Controller;
use App\Models\Jornada;
use Illuminate\Http\Request;

class AperturaController extends Controller
{
    public function show()
    {
        $jornadaAbierta = Jornada::where('estado', 'abierta')->first();
        return view('jornada.apertura', compact('jornadaAbierta'));
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'tasa_bcv' => 'required|numeric',
            'monto_inicial' => 'required|numeric',
        ]);

        Jornada::create([
            'usuario_apertura_id' => auth()->id(),
            'fecha_apertura' => now(),
            'tasa_bcv_apertura' => $request->tasa_bcv,
            'estado' => 'abierta',
        ]);

        return redirect()->route('dashboard');
    }
}
