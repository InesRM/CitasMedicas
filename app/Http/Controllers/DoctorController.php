<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Especialidad;

class DoctorController extends Controller
{
    public function getDoctores()
    {
        $doctores = User::where('rol', 'doctor')->get();
        return $doctores;
    }

    public function crear(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'dni'=> 'required',
            'password' => 'required',
            'especialidad' => '',
        ]);

        $doctor = new User();
        $doctor->name = $request->name;
        $doctor->email = $request->email;
        $doctor->dni = $request->dni;
        $doctor->password = bcrypt($request->password);
        $doctor->rol = 'doctor';
        $doctor->especialidad= $request->especialidad;
        $doctor->save();

        $doctor->especialidades()->attach($request->especialidad);

        return ('Doctor creado');
    }

    public function getDoctor($id)
    {
        $doctor = User::find($id);
        return $doctor;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'especialidad' => 'required',
        ]);

        $doctor = User::find($id);
        $doctor->name = $request->name;
        $doctor->email = $request->email;
        $doctor->save();

        $doctor->especialidades()->sync($request->especialidad);

        return ('Doctor actualizado');
    }

    public function delete($id)
    {
        $doctor = User::find($id);
        $doctor->especialidades()->detach();
        $doctor->delete();
        return ('Doctor eliminado');
    }

    public function getEspecialidades($id)
    {
        $doctor = User::find($id);
        $especialidades = $doctor->especialidades;
        return $especialidades;

    }

    public function addEspecialidad(Request $request, $id)
    {
        $request->validate([
            'especialidad' => 'required',
        ]);

        $doctor = User::find($id);
        $doctor->especialidades()->attach($request->especialidad);

        return ('Especialidad añadida');
    }

    public function deleteEspecialidad(Request $request, $id)
    {
        $request->validate([
            'especialidad' => 'required',
        ]);

        $doctor = User::find($id);
        $doctor->especialidades()->detach($request->especialidad);

        return ('Especialidad eliminada');
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
        $doctor = User::find($id);
        $citas = $doctor->citas;
        return $citas;
    }
}
