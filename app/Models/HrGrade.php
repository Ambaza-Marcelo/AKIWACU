<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrGrade extends Model
{
    //
    protected $fillable = [
        'name',
    ];


    public function employe(){
        return $this->hasMany('App\Models\HrEmploye','grade_id');
    }
}
