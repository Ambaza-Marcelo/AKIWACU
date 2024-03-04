<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTypePrime extends Model
{
    //
    protected $fillable = [
        'name',
    ];


    public function prime(){
        return $this->hasMany('App\Models\HrPrime','type_prime_id');
    }
}
