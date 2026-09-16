<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogo\StoreCategoriaRequest;
use App\Http\Requests\Catalogo\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        return view('catalogo.categorias');
    }

    public function data(Request $request)
    {
        return datatables()->eloquent(Categoria::query())
            ->addColumn('acciones', function ($categoria) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="editar" data-id="'.$categoria->id.'" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="'.$categoria->id.'" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function store(StoreCategoriaRequest $request)
    {
        Categoria::create($request->validated());

        return response()->json(['success' => true, 'message' => 'Categoría creada exitosamente.']);
    }

    public function show(Categoria $categoria)
    {
        return response()->json(['success' => true, 'data' => $categoria]);
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Categoría actualizada exitosamente.']);
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return response()->json(['success' => true, 'message' => 'Categoría eliminada exitosamente.']);
    }
}
