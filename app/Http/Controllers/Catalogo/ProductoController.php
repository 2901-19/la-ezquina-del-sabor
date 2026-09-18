<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogo\StoreProductoRequest;
use App\Http\Requests\Catalogo\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Configuracion;
use App\Models\Producto;
use App\Models\Receta;
use App\Services\PrecioService;
use App\Services\TasaBcvService;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::where('activa', true)->orderBy('nombre')->get();
        $recetas = Receta::orderBy('nombre')->get();

        return view('catalogo.productos', compact('categorias', 'recetas'));
    }

    public function data(Request $request)
    {
        $productos = Producto::with('categoria', 'receta');

        $tasaBcv = (float) Configuracion::obtener('tasa_bcv', 818);

        return datatables()->eloquent($productos)
            ->filterColumn('categoria_id', function ($query, $keyword) {
                $query->whereHas('categoria', function ($q) use ($keyword) {
                    $q->where('nombre', 'ilike', "%{$keyword}%");
                });
            })
            ->filterColumn('activo', function ($query, $keyword) {
                $query->where('activo', strtolower($keyword) === 'inactivo' ? false : true);
            })
            ->addColumn('precio_bs', function ($producto) use ($tasaBcv) {
                return round($producto->precio_usd * $tasaBcv, 2);
            })
            ->addColumn('acciones', function ($producto) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="editar" data-id="'.$producto->id.'" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn" data-act="ver" data-id="'.$producto->id.'" title="Ver">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="'.$producto->id.'" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function store(StoreProductoRequest $request)
    {
        $tasaBcv = app(TasaBcvService::class)->getTasaActual();
        $precio = app(PrecioService::class)->getPrecio(
            (object) ['tipo_precio' => $request->tipo_precio, 'precio_usd' => $request->precio_usd, 'margen_ganancia' => $request->margen_ganancia],
            $tasaBcv
        );

        Producto::create([
            'categoria_id' => $request->categoria_id,
            'receta_id' => $request->receta_id,
            'nombre' => $request->nombre,
            'tipo_precio' => $request->tipo_precio,
            'margen_ganancia' => $request->margen_ganancia,
            'precio_usd' => $request->precio_usd,
            'es_combo' => $request->boolean('es_combo'),
            'activo' => $request->boolean('activo', true),
        ]);

        return response()->json(['success' => true, 'message' => 'Producto creado exitosamente.']);
    }

    public function show(Producto $producto)
    {
        return response()->json(['success' => true, 'data' => $producto]);
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $producto->update([
            'categoria_id' => $request->categoria_id,
            'receta_id' => $request->receta_id,
            'nombre' => $request->nombre,
            'tipo_precio' => $request->tipo_precio,
            'margen_ganancia' => $request->margen_ganancia,
            'precio_usd' => $request->precio_usd,
            'es_combo' => $request->boolean('es_combo'),
            'activo' => $request->boolean('activo'),
        ]);

        return response()->json(['success' => true, 'message' => 'Producto actualizado exitosamente.']);
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente.']);
    }
}
