<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrService extends Model
{
    //
    protected $fillable = [
        'name',
        'code'
    ];


    public function employe(){
        return $this->hasMany('App\Models\HrEmploye','service_id');
    }
}
