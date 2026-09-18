<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreMermaRequest;
use App\Models\MateriaPrima;
use App\Models\MovimientoInventario;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class MermaController extends Controller
{
    public function store(StoreMermaRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $mp = MateriaPrima::lockForUpdate()->findOrFail($validated['materia_prima_id']);

            if ($mp->stock_actual < $validated['cantidad']) {
                throw new HttpResponseException(response()->json([
                    'errors' => ['cantidad' => ['Stock insuficiente. Disponible: '.number_format($mp->stock_actual, 2).' '.$mp->unidad_medida.'.']],
                ], 422));
            }

            $mp->decrement('stock_actual', $validated['cantidad']);
            $mp->update(['ultima_actualizacion' => now()]);

            MovimientoInventario::create([
                'materia_prima_id' => $validated['materia_prima_id'],
                'fecha_movimiento' => now(),
                'costo_unitario' => $mp->costo_unitario_usd,
                'cantidad_movimiento' => $validated['cantidad'],
                'tipo_movimiento' => 'merma',
                'nota' => ucfirst($validated['motivo']).($validated['notas'] ? '. '.$validated['notas'] : ''),
            ]);

            return response()->json(['success' => true, 'message' => 'Merma registrada exitosamente.']);
        });
    }

    public function show(MovimientoInventario $movimiento_inventario)
    {
        $movimiento_inventario->load('materiaPrima');

        return response()->json(['success' => true, 'data' => $movimiento_inventario]);
    }

    public function data()
    {
        return datatables()->eloquent(
            MovimientoInventario::with('materiaPrima')
                ->where('tipo_movimiento', 'merma')
                ->orderByDesc('fecha_movimiento')
        )
            ->addColumn('fecha', fn ($mov) => $mov->fecha_movimiento->format('d/m/Y H:i'))
            ->addColumn('materia_prima_nombre', function ($mov) {
                return $mov->materiaPrima->nombre ?? '-';
            })
            ->editColumn('cantidad_movimiento', fn ($mov) => '<span class="stock merma">'.number_format($mov->cantidad_movimiento, 2).'</span>')
            ->addColumn('acciones', function ($mov) {
                return '<div class="row-actions"><button class="icon-btn" data-act="ver-merma" data-id="'.$mov->id.'" title="Ver detalle"><i class="bi bi-eye"></i></button></div>';
            })
            ->rawColumns(['cantidad_movimiento', 'acciones'])
            ->make(true);
    }
}
