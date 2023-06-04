<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


//todo:para la autenticación debemos implementar JWTSubject

class User extends Authenticatable implements \Tymon\JWTAuth\Contracts\JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWtIdentifier()
    {
        return $this->getKey();
    }

    public function getJWtCustomClaims()
    {
        return [];
    }

    function scopeMedicos($query)
    {
        return $query->where('rol', 'doctor');
    }

    function scopePacientes($query)
    {
        return $query->where('rol', 'paciente');
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidad::class)->withTimestamps();
    }
    //Un médico atiende de 1 a N citas
//Una cita es atendida por un médico
    function asDoctorCitas()
    {
        return $this->hasMany(Cita::class, 'doctor_id');
    }

    function asPacienteCitas()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    function citasAtendidas()
    {
        return $this->asDoctorCitas()->where('estado', 'atendida');
    }

    function citasCanceladas()
    {
        return $this->asDoctorCitas()->where('estado', 'cancelada');
    }
}
