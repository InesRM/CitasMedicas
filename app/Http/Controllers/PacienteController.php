<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Especialidad;
use App\Models\Cita;


class PacienteController extends Controller
{

    public function getPacientes()
    {
        $pacientes = User::where('rol', 'paciente')->get();
        return $pacientes;
    }


    public function crear(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'dni'=> 'required',
            'password' => 'required',
        ]);

        $paciente = new User();
        $paciente->name = $request->name;
        $paciente->email = $request->email;
        $paciente->dni = $request->dni;
        $paciente->password = bcrypt($request->password);
        $paciente->rol = 'paciente';
        $paciente->save();

        return ('Paciente creado');
    }

    public function getPaciente($id)
    {
        $paciente = User::find($id);
        return $paciente;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $paciente = User::find($id);
        $paciente->name = $request->name;
        $paciente->email = $request->email;
        $paciente->save();

        return ('Paciente actualizado');
    }

    public function delete($id)
    {
        $paciente = User::find($id);
        $citas = Cita::where('paciente_id', $id)->get();
        foreach ($citas as $cita) {
            $cita->delete();
        }
        $paciente->delete();

        return ('Paciente eliminado');
    }

    public function getCitas($id_paciente)
    {
        $citas = Cita::where('paciente_id', $id_paciente)->get();

        return $citas;
    }

}
