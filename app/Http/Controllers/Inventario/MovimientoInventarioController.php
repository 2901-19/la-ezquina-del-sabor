<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;

class MovimientoInventarioController extends Controller
{
    public function data(Request $request)
    {
        return datatables()->eloquent(
            MovimientoInventario::with(['materiaPrima', 'compra'])
                ->when($request->filled('materia_prima_id'), fn ($q) => $q->where('materia_prima_id', $request->materia_prima_id))
                ->orderByDesc('fecha_movimiento')
        )
            ->editColumn('fecha_movimiento', fn ($mov) => $mov->fecha_movimiento->format('d/m/Y H:i'))
            ->addColumn('materia_prima', fn ($mov) => $mov->materiaPrima->nombre ?? '-')
            ->addColumn('tipo_label', function ($mov) {
                $mapa = ['entrada' => 'Entrada', 'salida' => 'Salida', 'merma' => 'Merma'];

                $clase = [
                    'entrada' => 'badge-entrada',
                    'salida' => 'badge-salida',
                    'merma' => 'badge-merma',
                ];

                return '<span class="badge '.$clase[$mov->tipo_movimiento].'">'.$mapa[$mov->tipo_movimiento].'</span>';
            })
            ->editColumn('cantidad_movimiento', fn ($mov) => '<span class="stock '.$mov->tipo_movimiento.'">'.number_format($mov->cantidad_movimiento, 2).'</span>')
            ->editColumn('costo_unitario', fn ($mov) => '$ '.number_format($mov->costo_unitario, 2))
            ->rawColumns(['tipo_label', 'cantidad_movimiento'])
            ->make(true);
    }
}
