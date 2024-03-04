<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrPrime extends Model
{
    //
    protected $fillable = [
        'somme_prime',
        'type_prime_id',
        'employe_id'
    ];

    public function typePrime(){
        return $this->belongsTo('App\Models\HrTypePrime');
    }

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }
}
