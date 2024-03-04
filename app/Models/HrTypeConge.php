<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTypeConge extends Model
{
    //
    protected $fillable = [
        'libelle',
        'description'
    ];


    public function takeConge(){
        return $this->hasMany('App\Models\HrTakeConge','type_conge_id');
    }
}
