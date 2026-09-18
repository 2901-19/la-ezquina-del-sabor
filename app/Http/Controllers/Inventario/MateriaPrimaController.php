<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreMateriaPrimaRequest;
use App\Http\Requests\Inventario\UpdateMateriaPrimaRequest;
use App\Models\MateriaPrima;
use Illuminate\Http\Request;

class MateriaPrimaController extends Controller
{
    public function index(Request $request)
    {
        $materiasPrimas = MateriaPrima::orderBy('nombre')->get();

        $criticas = $materiasPrimas->filter(fn ($mp) => $this->estadoStock($mp) === 'critico')->count();
        $bajas = $materiasPrimas->filter(fn ($mp) => $this->estadoStock($mp) === 'bajo')->count();

        return view('inventario.materias-primas', compact('materiasPrimas', 'criticas', 'bajas'));
    }

    public function show(MateriaPrima $materias_prima)
    {
        return response()->json(['success' => true, 'data' => $materias_prima]);
    }

    public function data(Request $request)
    {
        return datatables()->eloquent(MateriaPrima::query())
            ->addColumn('estado_stock', fn ($mp) => $this->estadoStock($mp))
            ->editColumn('stock_actual', fn ($mp) => '<span class="stock '.$this->estadoStock($mp).'">'.number_format($mp->stock_actual, 2).'</span>')
            ->editColumn('costo_unitario_usd', fn ($mp) => '$ '.number_format($mp->costo_unitario_usd, 2))
            ->addColumn('acciones', function ($mp) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="movimientos" data-id="'.$mp->id.'" data-nombre="'.e($mp->nombre).'" title="Movimientos">
                            <i class="bi bi-clock-history"></i>
                        </button>
                        <button class="icon-btn" data-act="editar" data-id="'.$mp->id.'" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="'.$mp->id.'" data-url="'.route('inventario.materias-primas.destroy', $mp->id).'" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['stock_actual', 'acciones'])
            ->make(true);
    }

    private function estadoStock(MateriaPrima $mp): string
    {
        if ($mp->stock_minimo <= 0) {
            return $mp->stock_actual <= 0 ? 'critico' : 'optimo';
        }

        if ($mp->stock_actual <= 0 || $mp->stock_actual <= $mp->stock_minimo * 0.5) {
            return 'critico';
        }

        if ($mp->stock_actual <= $mp->stock_minimo * 1.5) {
            return 'bajo';
        }

        return 'optimo';
    }

    public function store(StoreMateriaPrimaRequest $request)
    {
        MateriaPrima::create($request->validated());

        return response()->json(['success' => true, 'message' => 'Materia prima creada exitosamente.']);
    }

    public function update(UpdateMateriaPrimaRequest $request, MateriaPrima $materias_prima)
    {
        $materias_prima->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Materia prima actualizada exitosamente.']);
    }

    public function destroy(MateriaPrima $materias_prima)
    {
        $materias_prima->delete();

        return response()->json(['success' => true, 'message' => 'Materia prima eliminada exitosamente.']);
    }
}
