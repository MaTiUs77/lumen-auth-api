<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;
use Illuminate\Database\Eloquent\Model;

class Barrios extends Model
{
    protected $table = 'barrios';

    function Ciudad()
    {
        return $this->hasOne('App\Ciudad', 'id', 'ciudad_id');
    }
}
