<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\Request;

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
