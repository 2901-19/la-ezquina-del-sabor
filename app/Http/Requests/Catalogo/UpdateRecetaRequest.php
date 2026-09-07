<?php

namespace App\Http\Requests\Catalogo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'costo_total_usd' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la receta es obligatorio.',
        ];
    }
}
