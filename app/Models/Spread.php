<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spread extends Model
{
    protected $fillable = ['nombre', 'slug', 'descripcion', 'cantidad_cartas'];

    public function positions()
    {
        return $this->hasMany(SpreadPosition::class)->orderBy('orden');
    }
}