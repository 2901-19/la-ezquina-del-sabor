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
                        <button class="icon-btn" data-act="ver-producto" data-id="'.$producto->id.'" data-url="'.route('catalogo.productos.show', $producto->id).'" title="Ver">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="icon-btn" data-act="editar" data-id="'.$producto->id.'" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="'.$producto->id.'" data-url="'.route('catalogo.productos.destroy', $producto->id).'" title="Eliminar">
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
        $indexar = $request->boolean('indexar_costo_receta');
        $costo = $this->resolverCosto($request->receta_id, $request->costo_usd, $indexar);
        $precio = $this->calcularPrecio($request->tipo_precio, $request->precio_usd, $costo, $request->margen_ganancia);

        Producto::create([
            'categoria_id' => $request->categoria_id,
            'receta_id' => $request->receta_id,
            'indexar_costo_receta' => $indexar,
            'nombre' => $request->nombre,
            'tipo_precio' => $request->tipo_precio,
            'costo_usd' => $request->tipo_precio === 'margen' ? $costo : null,
            'margen_ganancia' => $request->margen_ganancia,
            'precio_usd' => $precio,
            'es_combo' => $request->boolean('es_combo'),
            'activo' => $request->boolean('activo', true),
        ]);

        return response()->json(['success' => true, 'message' => 'Producto creado exitosamente.']);
    }

    public function show(Producto $producto)
    {
        $producto->load('categoria', 'receta');

        $tasaBcv = (float) Configuracion::obtener('tasa_bcv', 818);
        $producto->precio_bs = round($producto->precio_usd * $tasaBcv, 2);

        return response()->json(['success' => true, 'data' => $producto]);
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $indexar = $request->boolean('indexar_costo_receta');
        $costo = $this->resolverCosto($request->receta_id, $request->costo_usd, $indexar);
        $precio = $this->calcularPrecio($request->tipo_precio, $request->precio_usd, $costo, $request->margen_ganancia);

        $producto->update([
            'categoria_id' => $request->categoria_id,
            'receta_id' => $request->receta_id,
            'indexar_costo_receta' => $indexar,
            'nombre' => $request->nombre,
            'tipo_precio' => $request->tipo_precio,
            'costo_usd' => $request->tipo_precio === 'margen' ? $costo : null,
            'margen_ganancia' => $request->margen_ganancia,
            'precio_usd' => $precio,
            'es_combo' => $request->boolean('es_combo'),
            'activo' => $request->boolean('activo'),
        ]);

        return response()->json(['success' => true, 'message' => 'Producto actualizado exitosamente.']);
    }

    public function destroy(Producto $producto)
    {
        $referencias = [];

        if ($producto->comandaDetalles()->exists()) {
            $referencias[] = 'comandas';
        }

        if ($producto->comboComponentes()->exists()) {
            $referencias[] = 'combos';
        }

        if (! empty($referencias)) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el producto porque está vinculado a '.implode(' y ', $referencias).'.',
            ], 409);
        }

        $producto->delete();

        return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente.']);
    }

    private function resolverCosto(?int $recetaId, ?float $costoManual, bool $indexar): ?float
    {
        if ($indexar && $recetaId) {
            $costoReceta = Receta::find($recetaId)?->costo_total_usd;

            if ($costoReceta !== null) {
                return (float) $costoReceta;
            }
        }

        return $costoManual !== null ? (float) $costoManual : null;
    }

    private function calcularPrecio(string $tipo, ?float $precioUsd, ?float $costoUsd, ?float $margen): float
    {
        if ($tipo === 'margen') {
            return app(PrecioService::class)->calcularPrecioMargen((float) $costoUsd, (float) $margen);
        }

        return (float) $precioUsd;
    }
}
