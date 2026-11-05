<?php

namespace Database\Factories;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Docente;

/**
 * @extends Factory<Estudiante>
 */
class EstudianteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'carne' => fake()->unique()->numerify('C#####'),
'nombre' => fake()->firstName(),
'apellidos' => fake()->lastName(),
'docente_id' => Docente::factory(),
        ];
    }
}
