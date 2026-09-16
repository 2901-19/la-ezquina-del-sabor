<?php

namespace Database\Factories;

use App\Models\MateriaPrima;
use Illuminate\Database\Eloquent\Factories\Factory;

class MateriaPrimaFactory extends Factory
{
    protected $model = MateriaPrima::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->words(2, true),
            'unidad_medida' => fake()->randomElement(['kg', 'litro', 'unidad', 'docena']),
            'stock_actual' => fake()->randomFloat(2, 0, 100),
            'stock_minimo' => fake()->randomFloat(2, 0, 10),
            'costo_unitario_usd' => fake()->randomFloat(2, 0.10, 10),
        ];
    }
}
