<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Centros extends Model
{
    protected $table = 'centros';

    function Barrio()
    {
        return $this->hasOne('App\Barrios', 'id', 'barrio_id');
    }
}
