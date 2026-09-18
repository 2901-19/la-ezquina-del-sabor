<?php

namespace Database\Factories;

use App\Models\MateriaPrima;
use App\Models\Receta;
use App\Models\RecetaDetalle;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecetaDetalleFactory extends Factory
{
    protected $model = RecetaDetalle::class;

    public function definition(): array
    {
        return [
            'receta_id' => Receta::factory(),
            'materia_prima_id' => MateriaPrima::factory(),
            'cantidad_requerida' => fake()->randomFloat(2, 0.01, 5),
        ];
    }
}
