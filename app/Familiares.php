<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;
use Illuminate\Database\Eloquent\Model;

class Familiares extends Model
{
    protected $table = 'familiars';

    function Personas()
    {
        return $this->hasOne('App\Personas', 'id', 'persona_id');
    }
}
