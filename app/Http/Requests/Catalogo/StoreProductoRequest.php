<?php

namespace App\Http\Requests\Catalogo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('permiso', 'editar_catalogo');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('productos', 'nombre')],
            'categoria_id' => 'required|integer|exists:categorias,id',
            'receta_id' => 'nullable|integer|exists:recetas,id',
            'indexar_costo_receta' => 'boolean',
            'tipo_precio' => 'required|in:margen,definido',
            'costo_usd' => 'required_if:tipo_precio,margen|nullable|numeric|min:0',
            'margen_ganancia' => 'required_if:tipo_precio,margen|nullable|numeric|min:0|max:200',
            'precio_usd' => 'required_if:tipo_precio,definido|nullable|numeric|min:0',
            'es_combo' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con ese nombre.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'tipo_precio.required' => 'El tipo de precio es obligatorio.',
            'costo_usd.required_if' => 'El costo en USD es obligatorio para precio por margen.',
            'margen_ganancia.required_if' => 'El margen de ganancia es obligatorio para precio por margen.',
            'precio_usd.required_if' => 'El precio USD es obligatorio para precio definido.',
        ];
    }
}
