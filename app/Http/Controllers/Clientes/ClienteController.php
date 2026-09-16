<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clientes\StoreClienteRequest;
use App\Http\Requests\Clientes\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::latest()->paginate(15);

        return view('clientes.index', compact('clientes'));
    }

    public function data(Request $request)
    {
        return datatables()->eloquent(Cliente::query())
            ->addColumn('compras', function ($cliente) {
                return $cliente->comandas()->count();
            })
            ->addColumn('saldo', function ($cliente) {
                return $cliente->creditos()->where('estado', 'pendiente')->sum('saldo_pendiente_usd');
            })
            ->addColumn('acciones', function ($cliente) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="editar" data-id="'.$cliente->id.'" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn" data-act="borrar" data-id="'.$cliente->id.'" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());

        return response()->json(['success' => true, 'message' => 'Cliente creado exitosamente.']);
    }

    public function show(Cliente $cliente)
    {
        return response()->json(['success' => true, 'data' => $cliente]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Cliente actualizado exitosamente.']);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json(['success' => true, 'message' => 'Cliente eliminado exitosamente.']);
    }
}
