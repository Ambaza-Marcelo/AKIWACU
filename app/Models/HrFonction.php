<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrFonction extends Model
{
    //
    protected $fillable = [
        'name'
    ];

    public function stagiaire(){
        return $this->hasMany('App\Models\HrStagiaire','fonction_id');
    }

    public function employe(){
        return $this->hasMany('App\Models\HrEmploye','fonction_id');
    }
}
