<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cita>
 */
class CitaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $date= $this->faker->dateTimeBetween($startDate = 'now', $endDate = '+1 years', $timezone = null);
        $fecha = $date->format('Y-m-d H:i:s');
        $hora= $date->format('H:i:s');
        return [
            'descripcion' => $this->faker->text(80),
            'fecha_inicio' => $fecha,
            'hora_inicio' => $hora,
            'type'=>$this->faker->randomElement(['Consulta','Examen','Operacion']),
            'paciente_id' => \App\Models\User::pacientes()->get()->random()->id,
            'doctor_id' => \App\Models\User::medicos()->get()->random()->id,
            'especialidad_id' => \App\Models\Especialidad::all()->random()->id,
            'estado' => $this->faker->randomElement(['pendiente', 'atendido', 'cancelado']),
        ];
    }
}
