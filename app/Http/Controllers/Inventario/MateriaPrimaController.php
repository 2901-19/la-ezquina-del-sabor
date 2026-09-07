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
        return view('inventario.materias-primas', compact('materiasPrimas'));
    }

    public function data(Request $request)
    {
        return datatables()->eloquent(MateriaPrima::query())
            ->addColumn('acciones', function ($mp) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="editar" data-id="' . $mp->id . '" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="' . $mp->id . '" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
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
