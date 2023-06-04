<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Especialidad;
use App\Models\Cita;
use Carbon\Carbon;
use App\Models\citasCanceladas;


class CitaController extends Controller
{
    public function getCitas()
    {
        try {
            $citas = Cita::all();
            return $citas;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede encontrar las citas'
            ], 404);
        }
    }

    public function agenda(Request $request)
    {
        try {
            $request->validate([
                'descripcion' => 'required',
                'fecha_inicio' => 'required',
                'hora_inicio' => 'required',
                'type' => 'required',
                'doctor_id' => 'exists:users,id',
                'paciente_id' => 'required',
                'especialidad_id' => 'exists:especialidades,id',

            ]);
            //control de citas con el servicio Agenda Servicio

            //comprobar que el doctor no tenga otra cita en la misma fecha y hora

            $cita = Cita::where('doctor_id', $request->doctor_id)
                ->where('fecha_inicio', $request->fecha_inicio)
                ->where('hora_inicio', $request->hora_inicio)
                ->first();

            if ($cita) {
                return response()->json([
                    'message' => 'El doctor ya tiene una cita en esa fecha y hora, elija una hora posterior, otro doctor u otra fecha',
                ], 409);
            }

            //comprobar que el paciente no tenga otra cita en la misma fecha y hora

            $cita = Cita::where('paciente_id', $request->paciente_id)
                ->where('fecha_inicio', $request->fecha_inicio)
                ->where('hora_inicio', $request->hora_inicio)
                ->first();

            if ($cita) {
                return response()->json([
                    'message' => 'El paciente ya tiene una cita en esa fecha y hora',
                ], 409);
            }

            //comprobar que la especialidad del doctor coincide con el id de la especialidad solicitada
            //recordar que hay tres doctores por especialidad y puede elegir cualquiera de los tres

            $doctor = User::find($request->doctor_id);
            if (!$doctor) {
                return response()->json([
                    'message' => 'El id del doctor no existe',
                ], 409);
            }
            $especialidad_id = $doctor->especialidades()->first()->id;
            if ($especialidad_id != $request->especialidad_id) {
                return response()->json([
                    'message' => 'La especialidad del doctor no coincide con la especialidad solicitada',
                ], 409);
            }

            $paciente = User::find($request->paciente_id);
            if (!$paciente) {
                return response()->json([
                    'message' => 'El id del paciente no existe',
                ], 409);
            }

            //controlar que el id_especialidad coincide con un id_especialidad de la tabla especialidades

            $especialidad = Especialidad::find($request->especialidad_id);
            if (!$especialidad) {
                return response()->json([
                    'message' => 'El id de la especialidad no existe',
                ], 409);
            }

            //controlar que la fecha de la cita sea posterior a la fecha actual

            $fecha_actual = Carbon::now();
            $fecha_cita = Carbon::parse($request->fecha_inicio);
            if ($fecha_cita->lessThan($fecha_actual)) {
                return response()->json([
                    'message' => 'La fecha de la cita no puede ser anterior a la fecha actual',
                ], 409);
            }

            //controlar que la fecha de la cita no sea un domingo

            $fecha_cita = Carbon::parse($request->fecha_inicio);
            if ($fecha_cita->dayOfWeek == 0) {
                return response()->json([
                    'message' => 'La fecha de la cita no puede ser un domingo, no es día laboral',
                ], 409);
            }
            $hora_cita = Carbon::parse($request->hora_inicio);
            $hora_cita->format('H:i:s');
            //controlar que la hora de la cita sea entre las 8:00 y las 20:00
            if ($hora_cita->hour < 8 || $hora_cita->hour > 20) {
                return response()->json([
                    'message' => 'La hora de la cita debe ser entre las 8:00 y las 20:00',
                ], 409);
            }

            $cita = new Cita();
            $cita->descripcion = $request->descripcion;
            $cita->fecha_inicio = $fecha_cita;
            $cita->hora_inicio = $hora_cita;
            $cita->type = $request->type;
            $cita->doctor_id = $request->doctor_id;
            $cita->paciente_id = $request->paciente_id;
            $cita->especialidad_id = $request->especialidad_id;
            $cita->save();

            return ('Cita creada');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la cita',
                'error' => $e->getMessage()
            ], 409);
        }
    }


    public function getCita($id)
    {
        try {
            $cita = Cita::find($id);
            if (!$cita) {
                return response()->json([
                    'message' => 'No se puede encontrar la cita especificada con ese Id'
                ], 404);
            }
            return $cita;
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede encontrar la cita indicada'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'descripcion' => 'required',
                'fecha_inicio' => 'required',
                'hora_inicio' => 'required',
                'type' => 'required',
                'doctor_id' => 'exists:users,id',
                'paciente_id' => 'required',
                'especialidad_id' => 'exists:especialidades,id',
            ]);

            $cita = Cita::find($id);
            if (!$cita) {
                return response()->json([
                    'message' => 'No se puede encontrar la cita especificada'
                ], 404);
            }
            $cita->descripcion = $request->descripcion;
            $cita->fecha_inicio = $request->fecha_inicio;
            $cita->hora_inicio = $request->hora_inicio;
            $cita->type = $request->type;
            $cita->doctor_id = $request->doctor_id;
            $cita->paciente_id = $request->paciente_id;
            $cita->especialidad_id = $request->especialidad_id;
            $cita->save();

            return ('Cita actualizada');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la cita',
                'error' => $e->getMessage()
            ], 409);
        }
    }


    public function delete($id)
    {
        try {
            $cita = Cita::find($id);
            if (!$cita) {
                return response()->json([
                    'message' => 'No se puede encontrar la cita indicada'
                ], 404);
            }
            $cita->delete();

            return ('Cita eliminada');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la cita',
                'error' => $e->getMessage()
            ], 409);
        }
    }


    //citas Canceladas

    //Cancelamos citas pendientes únicamente
    public function cancelarCita($id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json([
                'message' => 'El id de la cita no existe',
            ], 409);
        } else if ($cita->estado == 'cancelado') {
            return response()->json([
                'message' => 'La cita ya estaba cancelada previamente',
            ], 409);
        } else if ($cita->estado == 'atendido') {
            return response()->json([
                'message' => 'La cita ya ha sido atendida previamente',
            ], 409);
        } else {
            $cita->estado = 'cancelado';
            $citaCancelada = citasCanceladas::create([
                'cita_id' => $cita->id,
                'descripcion' => $cita->descripcion,
                'cancelada_por' => $cita->paciente_id, //Todo: no entiendo porqué no se graba el id del paciente....
            ]);
            $cita->save();
            $citaCancelada->save();
            return ('Cita cancelada');
        }
    }
    public function getCitasCanceladas()
    {
        try {
           $citasCanceladas=Cita::where('estado','cancelado')->get();
            return $citasCanceladas;

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las citas canceladas',
                'error' => $e->getMessage()
            ], 409);
        }

    }

    public function getCitaCancelada($id)
    {
        try {
            $cita = Cita::find($id);
            if (!$cita) {
                return response()->json([
                    'message' => 'Cita no encontrada',
                ], 409);
            }
            $cancelada = citasCanceladas::where('cita_id', $id)->first();
            if ($cita->estado != 'cancelado') {
                return response()->json([
                    'message' => 'La cita no está cancelada',
                ], 409);
            }
            return $cancelada;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la cita cancelada',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function getCitasDoctor($id)
    {
        try {
            $citas = Cita::where('doctor_id', $id)->get();
            if (!$citas) {
                return response()->json([
                    'message' => 'No se puede encontrar la cita de ese doctor'
                ], 404);
            }
            return $citas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las citas del doctor',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function getCitasPaciente($id)
    {
        try {
            $citas = Cita::where('paciente_id', $id)->get();
            if (!$citas) {
                return response()->json([
                    'message' => 'No se puede encontrar la cita de ese paciente'
                ], 404);
            }
            return $citas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las citas del paciente',
                'error' => $e->getMessage()
            ], 409);
        }

    }

    public function getCitasEspecialidad($id)
    {
        try {
            $citas = Cita::where('especialidad_id', $id)->get();
            if (!$citas) {
                return response()->json([
                    'message' => 'No se puede encontrar la cita de esa especialidad'
                ], 404);
            }
            return $citas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las citas de la especialidad',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function getCitasFecha($fecha)
    {
        try {
            $citas = Cita::where('fecha_inicio', $fecha)->get();
            if (!$citas) {
                return response()->json([
                    'message' => 'No se puede encontrar la cita de esa fecha'
                ], 404);
            }
            return $citas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las citas de la fecha',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function getCitasEstado($estado)
    {
        try {
            $citas = Cita::where('estado', $estado)->get();
            if (!$citas) {
                return response()->json([
                    'message' => 'No se pueden encontrar las citas'
                ], 404);
            }
            return $citas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las citas del estado',
                'error' => $e->getMessage()
            ], 409);
        }
    }
}
