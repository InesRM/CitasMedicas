<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class roles extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'guard_name',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'rol');
    }

    public function permissions()
    {
        return $this->belongsToMany(permissions::class, 'permission_role');
    }

    public function hasPermission($permission)
    {
        return $this->permissions()->where('name', $permission)->first() ? true : false;
    }


}
