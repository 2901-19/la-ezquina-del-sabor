<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Usuario extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'usuarios';
    protected $fillable = ['rol_id', 'username', 'password_hash', 'nombre_completo', 'activo'];
    protected $casts = ['activo' => 'boolean'];
    protected $hidden = ['password_hash'];

    public function rol()
    {
        return $this->belongsTo(Role::class);
    }

    public function jornadas(): HasMany
    {
        return $this->hasMany(Jornada::class);
    }

    public function comandas(): HasMany
    {
        return $this->hasMany(Comanda::class);
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
