<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Models\User;
use App\Models\Cita;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function getEspecialidades()
    {
        try {
            $especialidades = Especialidad::all();
            return $especialidades;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se pueden encontrar las especialidades'
            ], 404);
        }
    }

    public function getEspecialidad($id)
    {
        try {
            $especialidad = Especialidad::find($id);
            return $especialidad;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede encontrar la especialidad indicada'
            ], 404);
        }
    }

    public function crear(Request $request)
    {
        try {
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
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede crear la especialidad'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try{
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
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'No se puede actualizar la especialidad'
        ], 404);
    }
    }

    public function delete($id)
    {
        try{
        $especialidad = Especialidad::find($id);
        $especialidad->delete();

        return response()->json([
            'message' => 'Especialidad eliminada correctamente',
        ], 200);
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'No se puede eliminar la especialidad'
        ], 404);
    }
    }

    public function getDoctores($id)
    {
        try{
        $especialidad = Especialidad::find($id);
        $doctores = $especialidad->users;
        return $doctores;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede encontrar la especialidad indicada'
            ], 404);
        }
    }

    public function getCitas($id)
    {
        try{
        $especialidad = Especialidad::find($id);
        $citasX=Cita::all()->where('especialidad_id', $especialidad->id)->pluck('id');
        $citas = Cita::find($citasX);
        if ($citas == null) {
            return response()->json([
                'message' => 'No se puede encontrar la especialidad indicada o no tiene citas asociadas'
            ], 404);
        }
        return $citas;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede encontrar la especialidad indicada'
            ], 404);
        }
    }

}
