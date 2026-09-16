<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogo\StoreRecetaRequest;
use App\Http\Requests\Catalogo\UpdateRecetaRequest;
use App\Models\MateriaPrima;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index(Request $request)
    {
        $materiasPrimas = MateriaPrima::orderBy('nombre')->get();
        $recetas = Receta::orderBy('nombre')->get();

        return view('catalogo.recetas', compact('materiasPrimas', 'recetas'));
    }

    public function data(Request $request)
    {
        return datatables()->eloquent(Receta::withCount('recetaDetalles as ingredientes_count'))
            ->addColumn('costo_formateado', function ($receta) {
                return '$ '.number_format($receta->costo_total_usd, 2);
            })
            ->addColumn('acciones', function ($receta) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="ver-receta" data-id="'.$receta->id.'" data-url="'.route('catalogo.recetas.show', $receta->id).'" title="Ver">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="icon-btn" data-act="editar" data-id="'.$receta->id.'" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="'.$receta->id.'" data-url="'.route('catalogo.recetas.destroy', $receta->id).'" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function show(Receta $receta)
    {
        $receta->load([
            'recetaDetalles.materiaPrima',
            'recetaDetalles.recetaBase',
        ]);

        return response()->json([
            'success' => true,
            'data' => $receta,
        ]);
    }

    public function store(StoreRecetaRequest $request)
    {
        $receta = DB::transaction(function () use ($request) {
            $receta = Receta::create($request->validated());

            $this->syncDetalles($receta, $request->detalles ?? []);
            $receta->recalcularCosto();

            return $receta;
        });

        return response()->json([
            'success' => true,
            'message' => 'Receta creada exitosamente.',
            'data' => $receta->load('recetaDetalles'),
        ]);
    }

    public function update(UpdateRecetaRequest $request, Receta $receta)
    {
        $receta = DB::transaction(function () use ($request, $receta) {
            $receta->update($request->validated());

            $this->syncDetallesDiff($receta, $request->detalles ?? []);
            $receta->recalcularCosto();

            return $receta;
        });

        return response()->json([
            'success' => true,
            'message' => 'Receta actualizada exitosamente.',
            'data' => $receta->load('recetaDetalles'),
        ]);
    }

    public function destroy(Receta $receta)
    {
        $receta->delete();

        return response()->json(['success' => true, 'message' => 'Receta eliminada exitosamente.']);
    }

    private function syncDetalles(Receta $receta, array $detalles): void
    {
        foreach ($detalles as $detalle) {
            $receta->recetaDetalles()->create([
                'materia_prima_id' => $detalle['materia_prima_id'] ?? null,
                'receta_base_id' => $detalle['receta_base_id'] ?? null,
                'cantidad_requerida' => $detalle['cantidad_requerida'],
            ]);
        }
    }

    private function syncDetallesDiff(Receta $receta, array $detalles): void
    {
        $validIds = $receta->recetaDetalles()->pluck('id')->toArray();
        $enviosIds = [];

        foreach ($detalles as $detalle) {
            $id = $detalle['id'] ?? null;

            $data = [
                'materia_prima_id' => $detalle['materia_prima_id'] ?? null,
                'receta_base_id' => $detalle['receta_base_id'] ?? null,
                'cantidad_requerida' => $detalle['cantidad_requerida'],
            ];

            if ($id && in_array($id, $validIds)) {
                $receta->recetaDetalles()
                    ->where('id', $id)
                    ->update($data);

                $enviosIds[] = $id;

                continue;
            }

            $enviosIds[] = $receta->recetaDetalles()->create($data)->id;
        }

        if (! empty($enviosIds)) {
            $receta->recetaDetalles()
                ->whereNotIn('id', $enviosIds)
                ->delete();
        } else {
            $receta->recetaDetalles()->delete();
        }
    }
}
