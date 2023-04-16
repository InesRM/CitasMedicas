<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $citas = Cita::all();
        return $citas;
    }

    public function agenda(Request $request)
    {
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
        $especialidad_id = $doctor->especialidades()->first()->id;
        if ($especialidad_id != $request->especialidad_id) {
            return response()->json([
                'message' => 'La especialidad del doctor no coincide con la especialidad solicitada',
            ], 409);
        }

        //controlar que el id_doctor coincide con un id_doctor de la tabla users y que el id_paciente coincide con un id_paciente de la tabla users

        $doctor = User::find($request->doctor_id);
        if (!$doctor) {
            return response()->json([
                'message' => 'El id del doctor no existe',
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
        $cita->fecha_inicio = $request->fecha_inicio;
        $cita->hora_inicio = $request->hora_inicio;
        $cita->type = $request->type;
        $cita->doctor_id = $request->doctor_id;
        $cita->paciente_id = $request->paciente_id;
        $cita->especialidad_id = $request->especialidad_id;
        $cita->save();

        return ('Cita creada');
    }


    public function getCita($id)
    {
        $cita = Cita::find($id);
        return $cita;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required',
            'fecha_inicio' => 'required',
            'hora_inicio' => 'required',
            'type' => 'required',
            'doctor_id' => 'exists:users,id',
            'paciente_id' => 'required',
            'especialidad_id' => 'exists:especialidads,id',
        ]);

        $cita = Cita::find($id);
        $cita->descripcion = $request->descripcion;
        $cita->fecha_inicio = $request->fecha_inicio;
        $cita->hora_inicio = $request->hora_inicio;
        $cita->type = $request->type;
        $cita->doctor_id = $request->doctor_id;
        $cita->paciente_id = $request->paciente_id;
        $cita->especialidad_id = $request->especialidad_id;
        $cita->save();

        return ('Cita actualizada');
    }

    public function delete($id)
    {
        $cita = Cita::find($id);
        $cita->delete();

        return ('Cita eliminada');
    }

    //citas Canceladas

    public function getCitasCanceladas()
    {
        $citas = citasCanceladas::all();
        return $citas;

    }

    public function getCitaCancelada($id)
    {
        $cita = Cita::find($id);
        $cancelada=citasCanceladas::where('cita_id',$id)->first();
        if($cita->estado != 'cancelado'){
            return response()->json([
                'message' => 'La cita no está cancelada',
            ], 409);
        }
        return $cancelada;
    }
//Cancelamos citas pendientes únicamente
    public function cancelarCita($id){
        $cita = Cita::find($id);
        if($cita->estado == 'cancelado'){
            return response()->json([
                'message' => 'La cita ya estaba cancelada previamente',
            ], 409);
        }else if($cita->estado == 'atendido'){
            return response()->json([
                'message' => 'La cita ya ha sido atendida previamente',
            ], 409);
        }
        else{
            $cita->estado = 'cancelado';
            $citaCancelada=citasCanceladas::create([
                'cita_id' => $cita->id,
                'descripcion' => $cita->descripcion,
                'cancelada_por' => $cita->paciente_id, //Todo: cambiar por el usuario que cancela la cita, necesitamos un módulo auth para identificar usuarios
            ]);
            $cita->save();
            $citaCancelada->save();
            return ('Cita cancelada');
        }
    }

    public function getCitasDoctor($id)
    {
        $citas = Cita::where('doctor_id', $id)->get();
        return $citas;
    }

    public function getCitasPaciente($id)
    {
        $citas = Cita::where('paciente_id', $id)->get();
        return $citas;
    }

    public function getCitasEspecialidad($id)
    {
        $citas = Cita::where('especialidad_id', $id)->get();
        return $citas;
    }
}
