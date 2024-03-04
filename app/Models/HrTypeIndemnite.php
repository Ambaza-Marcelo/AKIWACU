<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTypeIndemnite extends Model
{
    //
    protected $fillable = [
        'name',
    ];


    public function indemnite(){
        return $this->hasMany('App\Models\HrIndemnite','type_indemnite_id');
    }
}
