<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Especialidad;
use App\Models\User;
use App\Factories\UserFactory;
class EspecialidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades=[
            'Oftalmología',
            'Pediatría',
            'Neurología',
        ];



        //Creamos 3 doctores por especialidad

        foreach ($especialidades as $especialidad) {
            $especialidad = Especialidad::factory()->create([
                'nombre' => $especialidad,
            ]);

            $especialidad->doctores()->saveMany(
                User::factory()->doctor()->count(3)->create()
            );

        }


}
}
