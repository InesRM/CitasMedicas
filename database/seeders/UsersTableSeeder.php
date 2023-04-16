<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
               //PARA CREAR EL PRIMER USER POR DEFECTO
        // ID 1
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@gmail.com',
            'password' =>bcrypt('123456'),
            'dni' => '00000001A',
            'rol' => 'administrador',

        ]);
                // ID 2

        User::create([
            'name' => 'Pacient Test',
            'email' => 'patient@gmail.com',
            'password' =>bcrypt('123456'),
            'dni' => '00000002A',
            'rol' => 'paciente',

        ]);

        // ID 3
        User::create([
            'name' => 'Doctor Test',
            'email' => 'doctor@gmail.com',
            'password' =>bcrypt('123456'),
            'dni' => '00000003A',
            'rol' => 'doctor',

        ]);
        //DESPUES SE CREAN ESTOS 10 REGISTROS
        User::factory(10)->create();

    }
}
