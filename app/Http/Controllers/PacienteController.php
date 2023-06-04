<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
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
        try{
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
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'No se puede crear al paciente, ya existe'
        ], 404);
    }
    }

    public function getPaciente($id)
    {
        try{
        $paciente = User::find($id);
        return $paciente;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede encontrar al paciente'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try{
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $paciente = User::find($id);
        $paciente->name = $request->name;
        $paciente->email = $request->email;
        $paciente->save();

        return ('Paciente actualizado');
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'No se puede actualizar al paciente'
        ], 404);
    }
    }

    public function delete($id)
    {
        try{
        $paciente = User::find($id);
        $citas = Cita::where('paciente_id', $id)->get();
        foreach ($citas as $cita) {
            $cita->delete();
        }
        $paciente->delete();

        return ('Paciente eliminado');
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'No se puede eliminar al paciente, tiene citas pendientes, pruebe dentro de unos días'
        ], 404);
    }
    }

    public function getCitas($id_paciente)
    {
        try{
        $citas = Cita::where('paciente_id', $id_paciente)->get();

        if ($citas->count() == 0) {
            return response()->json([
                'message' => 'El paciente no tiene citas asociadas'
            ], 404);
        }

        return $citas;
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'El paciente no tiene citas asociadas'
        ], 404);
    }
    }

}
