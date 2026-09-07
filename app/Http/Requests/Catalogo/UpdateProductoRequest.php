<?php

namespace App\Http\Requests\Catalogo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'receta_id' => 'nullable|integer|exists:recetas,id',
            'tipo_precio' => 'required|in:margen,definido',
            'margen_ganancia' => 'nullable|numeric|min:0|max:200',
            'precio_usd' => 'required|numeric|min:0',
            'es_combo' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'categoria_id.required' => 'La categoría es obligatoria.',
        ];
    }
}
