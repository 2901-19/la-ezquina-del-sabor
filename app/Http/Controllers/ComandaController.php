<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Jornada;
use App\Services\TasaBcvService;
use Illuminate\Http\Request;

class ComandaController extends Controller
{
    public function index()
    {
        return view('comandas.index');
    }

    public function data(Request $request)
    {
        $comandas = Comanda::with('cliente')
            ->select('id', 'numero_correlativo_diario', 'cliente_id', 'estado_comanda', 'total_usd', 'total_ve', 'fecha_creacion');

        return datatables()->eloquent($comandas)
            ->filterColumn('cliente_id', function ($query, $keyword) {
                $query->whereHas('cliente', function ($q) use ($keyword) {
                    $q->where('nombre', 'ilike', "%{$keyword}%");
                });
            })
            ->addColumn('acciones', function ($comanda) {
                return '<div class="row-actions">
                    <button class="icon-btn" data-act="ver" data-id="'.$comanda->id.'" title="Ver"><i class="bi bi-eye"></i></button>
                </div>';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function show(Comanda $comanda)
    {
        $comanda->load([
            'cliente',
            'usuario.rol',
            'comandaDetalles.producto',
        ]);

        return response()->json($comanda);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'nombre_cliente_temporal' => 'nullable|string|max:255',
            'telefono_delivery' => 'nullable|string|max:20',
            'notas_generales' => 'nullable|string|max:500',
        ]);

        $jornada = Jornada::where('estado', 'abierta')->latest()->first();

        if (! $jornada) {
            return response()->json(['success' => false, 'message' => 'No hay jornada abierta. Debe abrir una jornada antes de crear comandas.'], 422);
        }

        $comanda = Comanda::create([
            'jornada_id' => $jornada->id,
            'cliente_id' => $validated['cliente_id'] ?? null,
            'usuario_id' => auth()->id(),
            'tasa_bcv_aplicada' => app(TasaBcvService::class)->getTasaActual(),
            'numero_correlativo_diario' => Comanda::where('jornada_id', $jornada->id)->count() + 1,
            'nombre_cliente_temporal' => $validated['nombre_cliente_temporal'] ?? null,
            'telefono_delivery' => $validated['telefono_delivery'] ?? null,
            'notas_generales' => $validated['notas_generales'] ?? null,
            'estado_comanda' => 'montar',
            'total_usd' => 0,
            'total_ve' => 0,
        ]);

        return response()->json(['success' => true, 'message' => 'Comanda #'.$comanda->numero_correlativo_diario.' creada exitosamente.']);
    }

    public function cocina()
    {
        return view('comandas.cocina');
    }
}
