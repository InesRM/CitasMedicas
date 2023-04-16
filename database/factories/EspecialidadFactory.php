<?php

namespace Database\Factories;

use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EspecialidadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Especialidad::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->unique()->randomElement(['Oftalmología', 'Pediatría', 'Neurología']),
            'descripcion' => $this->faker->text(40),
        ];
    }

    public function withDoctor()
    {
        return $this->hasAttached(
            User::factory()->doctor(),
            'especialidades'
        );
    }

    public function withDoctors()
    {
        return $this->hasAttached(
            User::factory()->doctor()->count(3),
            'especialidades'
        );
    }


}
