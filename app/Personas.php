<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personas extends Model
{
    protected $table = 'personas';

    function Ciudad()
    {
        return $this->hasOne('App\Ciudad', 'id', 'ciudad_id');
    }
    function Barrio()
    {
        return $this->hasOne('App\Barrios', 'id', 'barrio_id');
    }
}
