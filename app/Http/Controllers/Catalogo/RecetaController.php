<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogo\StoreRecetaRequest;
use App\Http\Requests\Catalogo\UpdateRecetaRequest;
use App\Models\Receta;
use Illuminate\Http\Request;

class RecetaController extends Controller
{
    public function index(Request $request)
    {
        return view('catalogo.recetas');
    }

    public function data(Request $request)
    {
        return datatables()->eloquent(Receta::withCount('recetaDetalles as ingredientes_count'))
            ->addColumn('acciones', function ($receta) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="editar" data-id="' . $receta->id . '" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="' . $receta->id . '" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function store(StoreRecetaRequest $request)
    {
        Receta::create($request->validated());
        return response()->json(['success' => true, 'message' => 'Receta creada exitosamente.']);
    }

    public function update(UpdateRecetaRequest $request, Receta $receta)
    {
        $receta->update($request->validated());
        return response()->json(['success' => true, 'message' => 'Receta actualizada exitosamente.']);
    }

    public function destroy(Receta $receta)
    {
        $receta->delete();
        return response()->json(['success' => true, 'message' => 'Receta eliminada exitosamente.']);
    }
}
