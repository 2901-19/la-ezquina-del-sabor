<?php

namespace App\Http\Controllers\Jornada;

use App\Http\Controllers\Controller;
use App\Models\Jornada;

class CierreController extends Controller
{
    public function show()
    {
        $jornadaAbierta = Jornada::where('estado', 'abierta')->first();
        return view('jornada.cierre', compact('jornadaAbierta'));
    }
}
