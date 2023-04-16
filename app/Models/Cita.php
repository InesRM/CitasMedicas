<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\citasCanceladas;

class Cita extends Model
{
    use HasFactory;
    protected $fillable = [
        'descripcion',
        'fecha_inicio',
        'hora_inicio',
        'doctor_id',
        'paciente_id',
        'especialidad_id',
        'estado',
    ];

    //Una especialidad se asocia a muchas citas
    //1 citas pertenecen a una especialidad
    public function especialidad()
    {
        return $this->belongsTo(especialidad::class);
    }

    // Una cita es atendida por un solo doctor
    //1 doctor atiende a muchas citas
    //n citas->user(doctor) 1

     public function doctor()
    {
        return $this->belongsTo(User::class);

    }

    //cita->id
    //n citas->user(paciente) 1

    public function paciente()
    {
        return $this->belongsTo(User::class);
    }
    /* Como queremos mostrar la hora en formato de AM PM necesitamos
    que ese String que nos devuelve lo transformemos a un objeto carbon
    y para ello vamos a usar un accesor, los accesor deben tener la
    siguiente forma getXAttribute donde X es el nombre del metodo
    que usaremos para despues hacer posible lo siguiente:
    $cita-horario_time_12*/

    public function getHorarioTime12Attribute()
    {
        return Carbon::parse($this->hora_inicio)->format('h:i A');
    }

    //relacion 1-1/0
    //una cita se va a relacionar con una o ninguna cancelacion
    //cita->cancelacion->cancelada_por

    public function cancelacion()
    {
        return $this->hasOne(citasCanceladas::class);
    }

}



