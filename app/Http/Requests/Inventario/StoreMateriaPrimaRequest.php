<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class StoreMateriaPrimaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:materias_primas,nombre',
            'unidad_medida' => 'required|string|max:50',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'costo_unitario_usd' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la materia prima es obligatorio.',
            'nombre.unique' => 'Ya existe una materia prima con ese nombre.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
        ];
    }
}
