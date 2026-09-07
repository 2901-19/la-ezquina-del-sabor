<?php

namespace App\Http\Controllers;

use App\Models\Credito;
use Illuminate\Http\Request;

class CreditoController extends Controller
{
    public function index()
    {
        return view('creditos.index');
    }

    public function data(Request $request)
    {
        $creditos = Credito::with('cliente');

        return datatables()->eloquent($creditos)
            ->addColumn('acciones', function ($credito) {
                return '<div class="row-actions">
                    <button class="icon-btn" data-act="ver" data-id="'.$credito->id.'" title="Ver"><i class="bi bi-eye"></i></button>
                </div>';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
}
