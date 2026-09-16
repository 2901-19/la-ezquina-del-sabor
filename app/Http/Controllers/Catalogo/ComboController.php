<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::where('activa', true)->orderBy('nombre')->get();

        return view('catalogo.combos', compact('categorias'));
    }

    public function data(Request $request)
    {
        return datatables()->eloquent(Producto::where('es_combo', true)->with('categoria'))
            ->addColumn('acciones', function ($combo) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="editar" data-id="'.$combo->id.'" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="'.$combo->id.'" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'precio_usd' => 'required|numeric|min:0',
        ]);
        Producto::create([
            'categoria_id' => $validated['categoria_id'],
            'nombre' => $validated['nombre'],
            'tipo_precio' => 'definido',
            'precio_usd' => $validated['precio_usd'],
            'es_combo' => true,
            'activo' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Combo creado exitosamente.']);
    }

    public function show(Producto $combo)
    {
        return response()->json(['success' => true, 'data' => $combo]);
    }

    public function update(Request $request, Producto $combo)
    {
        $combo->update($request->validate(['nombre' => 'required|string|max:255']));

        return response()->json(['success' => true, 'message' => 'Combo actualizado exitosamente.']);
    }

    public function destroy(Producto $combo)
    {
        $combo->delete();

        return response()->json(['success' => true, 'message' => 'Combo eliminado exitosamente.']);
    }
}
