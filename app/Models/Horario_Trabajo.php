<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario_Trabajo extends Model
{
    use HasFactory;
    protected $table = 'horario_trabajo';

   protected $fillable = [
        'dia',
        'activo',
        'mañana_inicio',
        'mañana_fin',
        'tarde_inicio',
        'tarde_fin',
        'user_id',
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
