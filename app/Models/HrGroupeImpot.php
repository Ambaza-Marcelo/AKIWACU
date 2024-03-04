<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrGroupeImpot extends Model
{
    //
    protected $fillable = [
        'designation',
        'classe_inferieure',
        'classe_superieure'
    ];


    public function impot(){
        return $this->hasMany('App\Models\HrImpot','groupe_impot_id');
    }
}
