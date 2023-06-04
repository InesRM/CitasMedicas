<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Especialidad;
use App\Models\Cita;

class DoctorController extends Controller
{
    public function getDoctores()
    {
        try {
            $doctores = User::where('rol', 'doctor')->get();
            return $doctores;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se encuentran los doctores'
            ], 404);
        }
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'dni' => 'required',
            'password' => 'required',
            'especialidad' => 'required',
        ]);

        $doctor = new User();
        $doctor->name = $request->name;
        $doctor->email = $request->email;
        $doctor->dni = $request->dni;
        $doctor->password = bcrypt($request->password);
        $doctor->rol = 'doctor';
        $doctor->save();

        $doctor->especialidades()->attach($request->especialidad);

        return ('doctor creado');
    }

    public function getDoctor($id)
    {
        try {
            $doctor = User::find($id);
            if (!$doctor) {
                return response()->json([
                    'message' => 'No se encuentra el doctor'
                ], 404);
            }
            return $doctor;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No existe esa consulta'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'especialidad' => 'required',
            ]);

            $doctor = User::find($id);

            if (!$doctor || $doctor->rol != 'doctor') {
                return response()->json([
                    'message' => 'No se puede encontrar al doctor'
                ], 404);
            }
            $doctor->name = $request->name;
            $doctor->email = $request->email;
            $doctor->save();

            $doctor->especialidades()->sync($request->especialidad); //creamos la especialidad asociada al doctor
            return ('Doctor actualizado');
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede actualizar al doctor'
            ], 404);
        }
    }

    public function delete($id)
    {
        try {
            $doctor = User::find($id);
            if (!$doctor) {
                return response()->json([
                    'message' => 'Este doctor no existe'
                ], 404);
            }
            $doctor->delete();
            if ($doctor->rol == 'doctor') {
                $doctor->especialidades()->detach();
            }
            $doctor->especialidades()->detach();
            return ('Doctor eliminado');
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar al doctor, tiene citas pendientes, elimine las citas primero'
            ], 404);
        }
    }

    public function getEspecialidades($id)
    {
        try {
            $doctor = User::find($id);
            if (!$doctor) {
                return response()->json([
                    'message' => 'No existe el doctor'
                ], 404);
            }
            $especialidades = $doctor->especialidades;
            if(!$especialidades){
                return response()->json([
                    'message' => 'No se encuentran las especialidades asociadas a este doctor'
                ], 404);
            }
            return $especialidades;

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se encuentran las especialidades asociadas a este doctor'
            ], 404);
        }

    }

    public function addEspecialidad(Request $request, $id)
    {
        try {
            $request->validate([
                'especialidad' => 'required',
            ]);

            $doctor = User::find($id);
            $doctor->especialidades()->attach($request->especialidad);

            return ('Especialidad añadida');

            if (!$doctor) {
                return response()->json([
                    'message' => 'No se encuentra el doctor'
                ], 404);
            }
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Hay un error en la consulta'
            ], 404);
        }
    }

    public function deleteEspecialidad(Request $request, $id)
    {
        try {
            $request->validate([
                'especialidad' => 'required',
            ]);

            $doctor = User::find($id);
            $doctor->especialidades()->detach($request->especialidad);

            return ('Especialidad eliminada');
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar la especialidad'
            ], 404);
        }
    }

    public function getEspecialidadesDisponibles($id)
    {
        $doctor = User::find($id);
        $especialidades = Especialidad::all();
        $especialidadesDoctor = $doctor->especialidades;
        $especialidadesDisponibles = $especialidades->diff($especialidadesDoctor);
        return $especialidadesDisponibles;
    }

    public function getCitas($id)
    {
        try {
            $citas = Cita::where('doctor_id', $id)->get();
            if (!$citas) {
                return response()->json([
                    'message' => 'No se encuentran las citas'
                ], 404);
            }
            return $citas;

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se encuentran las citas'
            ], 404);
        }
    }

    public function addCita($id, Request $request){
        try {
            $cita = new Cita();
            $cita->descripcion = $request->descripcion;
            $cita->fecha_inicio = $request->fecha_inicio;
            $cita->hora_inicio = $request->hora_inicio;
            $cita->type = $request->type;
            $cita->doctor_id = $id;
            $cita->paciente_id = $request->paciente_id;
            $cita->especialidad()->associate($request->especialidad_id);
            $cita->estado = 'reservada';
            $cita->save();
            return ('Cita añadida');
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede añadir la cita, los datos no son correctos'
            ], 404);
        }
    }

    public function getCitasPendientes($id)
    {
        try {
            $citas = Cita::where('doctor_id', $id)->where('estado', 'reservada')->get();
            if(!$citas){
                return response()->json([
                    'message' => 'No hay citas reservadas en este momento'
                ], 404);
            }
            return $citas;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se ha podido realizar esta consulta'
            ], 404);
        }
    }

    public function getCitasAtendidas($id)
    {
        try {
            $citas = Cita::where('doctor_id', $id)->where('estado', 'atendido')->get();
            if($citas->isEmpty()){
                return response()->json([
                    'message' => 'No hay citas atendidas en este momento'
                ], 404);
            }
            return $citas;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se encuentran las citas'
            ], 404);
        }
    }

    public function getCitasCanceladas($id)
    {
        try {
            $citas = Cita::where('doctor_id', $id)->where('estado', 'cancelado')->get();
            if($citas->isEmpty()){
                return response()->json([
                    'message' => 'No hay citas canceladas en este momento'
                ], 404);
            }
            return $citas;

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se encuentran las citas'
            ], 404);
        }
    }

    public function updateCita(Request $request, $id)
    {
      try{

            $cita = Cita::find($id);
            $cita->descripcion = $request->descripcion;
            $cita->fecha_inicio = $request->fecha_inicio;
            $cita->hora_inicio = $request->hora_inicio;
            $cita->type = $request->type;
            $cita->doctor_id = $request->doctor_id;
            $cita->paciente_id = $request->paciente_id;
            $cita->especialidad()->associate($request->especialidad_id);
            $cita->estado = $request->estado;
            $cita->save();
            return ('Cita actualizada');
      }
        catch(QueryException $e){
            return response()->json([
                'message' => 'No se puede actualizar la cita'
            ], 404);
        }

}
}
