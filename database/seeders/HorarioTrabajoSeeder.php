<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Horario_Trabajo;

class HorarioTrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Esto sería una prueba con un horario por defecto para el médico 3 doctortest
        //Horario para una semana completa
        for ($i = 0; $i < 7; $i++) {
            Horario_Trabajo::Create(
                [
                    'dia' => $i,
                    'activo' => ($i == 3),// Jueves(tercer día) , (0=> Lunes,  6=> Domingo)
                    'mañana_inicio' => ($i == 3 ? '07:00:00' : '05:00:00'),
                    'mañana_fin' => ($i == 3 ? '09:30:00' : '05:00:00'),
                    'tarde_inicio' => ($i == 3 ? '15:00:00' : '13:00:00'),
                    'tarde_fin' => ($i == 3 ? '18:00:00' : '13:00:00'),
                    'user_id' => 3 // medico para probar de (USerTableSeeder)
                ]
            );


        }
    }
}
