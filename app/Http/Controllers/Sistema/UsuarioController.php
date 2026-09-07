<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sistema\StoreUsuarioRequest;
use App\Http\Requests\Sistema\UpdateUsuarioRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        return view('sistema.usuarios');
    }

    public function data(Request $request)
    {
        return datatables()->eloquent(Usuario::with('rol'))
            ->addColumn('acciones', function ($usuario) {
                return '
                    <div class="row-actions">
                        <button class="icon-btn" data-act="editar" data-id="' . $usuario->id . '" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="icon-btn del" data-act="borrar" data-id="' . $usuario->id . '" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function store(StoreUsuarioRequest $request)
    {
        Usuario::create([
            'rol_id' => $request->rol_id,
            'username' => $request->username,
            'password_hash' => bcrypt($request->password),
            'nombre_completo' => $request->nombre_completo,
            'activo' => true,
        ]);
        return response()->json(['success' => true, 'message' => 'Usuario creado exitosamente.']);
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password_hash'] = bcrypt($data['password']);
            unset($data['password']);
        } else {
            unset($data['password']);
        }
        $usuario->update($data);
        return response()->json(['success' => true, 'message' => 'Usuario actualizado exitosamente.']);
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->json(['success' => true, 'message' => 'Usuario eliminado exitosamente.']);
    }
}
