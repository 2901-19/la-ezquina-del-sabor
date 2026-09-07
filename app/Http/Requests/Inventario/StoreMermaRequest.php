<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class StoreMermaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'materia_prima_id' => 'required|exists:materias_primas,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|in:desperdicio,deterioro,caducidad,error',
            'notas' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'materia_prima_id.required' => 'Debes seleccionar una materia prima.',
            'materia_prima_id.exists' => 'La materia prima seleccionada no existe.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser mayor a cero.',
            'motivo.required' => 'El motivo es obligatorio.',
            'motivo.in' => 'El motivo seleccionado no es válido.',
        ];
    }
}
