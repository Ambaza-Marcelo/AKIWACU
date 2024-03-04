<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrFiliere extends Model
{
    //
    protected $fillable = [
        'nom'
    ];

    public function stagiaire(){
        return $this->hasMany('App\Models\HrStagiaire','filiere_id');
    }
}
