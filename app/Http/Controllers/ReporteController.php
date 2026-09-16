<?php

namespace App\Http\Controllers;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function exportar($tipo)
    {
        return response()->download('reporte.'.$tipo, 'reporte.'.$tipo);
    }
}
