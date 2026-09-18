<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreCompraRequest;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\MateriaPrima;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function store(StoreCompraRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $costoTotal = $validated['cantidad'] * $validated['costo_unitario_usd'];

            $compra = Compra::create([
                'fecha_compra' => $validated['fecha_compra'] ?? now(),
                'total' => $costoTotal,
                'referencia' => $validated['notas'] ?? null,
            ]);

            CompraDetalle::create([
                'compra_id' => $compra->id,
                'materia_prima_id' => $validated['materia_prima_id'],
                'cantidad' => $validated['cantidad'],
                'costo_unitario' => $validated['costo_unitario_usd'],
                'costo_total' => $costoTotal,
            ]);

            $mp = MateriaPrima::findOrFail($validated['materia_prima_id']);
            $mp->increment('stock_actual', $validated['cantidad']);
            $mp->update(['costo_unitario_usd' => $validated['costo_unitario_usd']]);

            MovimientoInventario::create([
                'materia_prima_id' => $validated['materia_prima_id'],
                'compra_id' => $compra->id,
                'fecha_movimiento' => now(),
                'costo_unitario' => $validated['costo_unitario_usd'],
                'cantidad_movimiento' => $validated['cantidad'],
                'tipo_movimiento' => 'entrada',
                'nota' => $validated['notas'] ?? null,
            ]);

            return response()->json(['success' => true, 'message' => 'Compra registrada exitosamente.']);
        });
    }

    public function show(Compra $compra)
    {
        $compra->load('compraDetalles.materiaPrima');

        return response()->json(['success' => true, 'data' => $compra]);
    }

    public function data()
    {
        return datatables()->eloquent(
            Compra::with('compraDetalles.materiaPrima')->orderByDesc('fecha_compra')
        )
            ->addColumn('fecha_compra', fn ($compra) => $compra->fecha_compra->format('d/m/Y'))
            ->editColumn('total', fn ($compra) => '$ '.number_format($compra->total, 2))
            ->addColumn('materia_prima', function ($compra) {
                return $compra->compraDetalles->pluck('materiaPrima.nombre')->implode(', ');
            })
            ->addColumn('acciones', function ($compra) {
                return '<div class="row-actions"><button class="icon-btn" data-act="ver-compra" data-id="'.$compra->id.'" title="Ver detalle"><i class="bi bi-eye"></i></button></div>';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
}
