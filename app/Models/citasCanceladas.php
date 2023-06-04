<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class citasCanceladas extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'cita_id',
        'cancelada_por_id',
    ];

    //todo: creo que este es el problema...PRUEBA: cambiar el nombre de la función por cancelada_por_id

    public function cancelada_por()
    {
        return $this->belongsTo(User::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
