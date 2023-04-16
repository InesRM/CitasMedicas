<?php

namespace Database\Factories;

use App\Models\Especialidad;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\User::class;
    public function definition(): array
    {

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('1234'), // password
            'remember_token' => Str::random(10),
            'dni' => fake()->unique()->randomNumber(8).fake()->randomLetter(),
            'rol' => 'paciente',
        ];
    }

  // cada vez que se ejecute este estado va tener un valor que es paciente (override)
    public function paciente(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'rol' => 'paciente',
            ];
        });
    }


// cada vez que se ejecute este estado va tener un valor que es doctor (override)
    public function doctor(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'rol' => 'doctor',
            ];
        });
    }



    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
