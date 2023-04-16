<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function getEspecialidades()
    {
        $especialidades = Especialidad::all();
        return $especialidades;
    }

    public function getEspecialidad($id)
    {
        $especialidad = Especialidad::find($id);
        return $especialidad;
    }

    public function crear(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
        ]);

        $especialidad = new Especialidad();
        $especialidad->nombre = $request->nombre;
        $especialidad->descripcion = $request->descripcion;
        $especialidad->save();

        return response()->json([
            'message' => 'Especialidad creada correctamente',
            'especialidad' => $especialidad,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
        ]);

        $especialidad = Especialidad::find($id);
        $especialidad->nombre = $request->nombre;
        $especialidad->descripcion = $request->descripcion;
        $especialidad->save();

        return response()->json([
            'message' => 'Especialidad actualizada correctamente',
            'especialidad' => $especialidad,
        ], 200);
    }

    public function delete($id)
    {
        $especialidad = Especialidad::find($id);
        $especialidad->delete();

        return response()->json([
            'message' => 'Especialidad eliminada correctamente',
        ], 200);
    }

}
