<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreMermaRequest;
use App\Models\MateriaPrima;
use App\Models\MovimientoInventario;

class MermaController extends Controller
{
    public function store(StoreMermaRequest $request)
    {
        $validated = $request->validated();

        $mp = MateriaPrima::findOrFail($validated['materia_prima_id']);
        $mp->decrement('stock_actual', $validated['cantidad']);

        MovimientoInventario::create([
            'materia_prima_id' => $validated['materia_prima_id'],
            'fecha_movimiento' => now(),
            'costo_unitario' => $mp->costo_unitario_usd,
            'cantidad_movimiento' => $validated['cantidad'],
            'tipo_movimiento' => 'merma',
            'nota' => ucfirst($validated['motivo']).($validated['notas'] ? '. '.$validated['notas'] : ''),
        ]);

        return response()->json(['success' => true, 'message' => 'Merma registrada exitosamente.']);
    }

    public function data()
    {
        return datatables()->eloquent(
            MovimientoInventario::with('materiaPrima')
                ->where('tipo_movimiento', 'merma')
                ->orderByDesc('fecha_movimiento')
        )
            ->addColumn('materia_prima_nombre', function ($mov) {
                return $mov->materiaPrima->nombre ?? '-';
            })
            ->addColumn('acciones', function ($mov) {
                return '<div class="row-actions"><button class="icon-btn" data-act="ver" data-id="'.$mov->id.'" title="Ver detalle"><i class="bi bi-eye"></i></button></div>';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
}
