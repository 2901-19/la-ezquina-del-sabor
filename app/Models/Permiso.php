<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    protected $table = 'permisos';
    protected $fillable = ['codigo', 'descripcion'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permiso_rol');
    }
}
