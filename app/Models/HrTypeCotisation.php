<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTypeCotisation extends Model
{
    //
    protected $fillable = [
        'name',
    ];


    public function cotisation(){
        return $this->hasMany('App\Models\HrCotisation','type_cotisation_id');
    }
}
