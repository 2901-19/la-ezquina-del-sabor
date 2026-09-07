<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $fillable = ['nombre', 'activa'];
    protected $casts = ['activa' => 'boolean'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}
