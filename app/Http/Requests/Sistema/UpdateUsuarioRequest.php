<?php

namespace App\Http\Requests\Sistema;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:usuarios,username,' . $this->route('usuario')?->id,
            'password' => 'nullable|string|min:6',
            'rol_id' => 'required|integer|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'Ya existe un usuario con ese nombre de usuario.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
