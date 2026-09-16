<?php

namespace App\Http\Requests\Catalogo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('permiso', 'editar_catalogo');
    }

    public function rules(): array
    {
        $recetaId = $this->route('receta')?->id;

        return [
            'nombre' => 'required|string|max:255|unique:recetas,nombre,'.$recetaId,
            'descripcion' => 'nullable|string|max:500',
            'detalles' => 'nullable|array|min:1',
            'detalles.*.materia_prima_id' => 'nullable|integer|exists:materias_primas,id',
            'detalles.*.receta_base_id' => 'nullable|integer|exists:recetas,id',
            'detalles.*.cantidad_requerida' => 'required|numeric|min:0.01',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! empty($this->detalles)) {
                foreach ($this->detalles as $i => $detalle) {
                    if (empty($detalle['materia_prima_id']) && empty($detalle['receta_base_id'])) {
                        $validator->errors()->add(
                            "detalles.{$i}.materia_prima_id",
                            'Cada ingrediente debe tener una materia prima o una sub-receta.'
                        );
                    }
                    if (! empty($detalle['receta_base_id']) && $detalle['receta_base_id'] == $this->route('receta')?->id) {
                        $validator->errors()->add(
                            "detalles.{$i}.receta_base_id",
                            'Una receta no puede usar a sí misma como ingrediente.'
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la receta es obligatorio.',
            'nombre.unique' => 'Ya existe una receta con ese nombre.',
            'detalles.*.cantidad_requerida.required' => 'La cantidad es obligatoria para cada ingrediente.',
            'detalles.*.cantidad_requerida.min' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}
