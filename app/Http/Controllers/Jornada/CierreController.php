<?php

namespace App\Http\Controllers\Jornada;

use App\Http\Controllers\Controller;
use App\Models\Jornada;
use Illuminate\Http\Request;

class CierreController extends Controller
{
    public function show()
    {
        $jornadaAbierta = Jornada::where('estado', 'abierta')->first();

        return view('jornada.cierre', compact('jornadaAbierta'));
    }

    public function cerrar(Request $request)
    {
        $request->validate([
            'tasa_bcv_cierre' => 'required|numeric',
            'monto_final' => 'required|numeric',
        ]);

        $jornada = Jornada::where('estado', 'abierta')->firstOrFail();

        $jornada->update([
            'usuario_cierre_id' => auth()->id(),
            'fecha_cierre' => now(),
            'tasa_bcv_cierre' => $request->tasa_bcv_cierre,
            'monto_final' => $request->monto_final,
            'estado' => 'cerrada',
        ]);

        return redirect()->route('dashboard');
    }
}
