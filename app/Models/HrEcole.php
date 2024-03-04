<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrEcole extends Model
{
    //
    protected $fillable = [
        'nom',
        'etat',
        'description',
        'adresse'
    ];

    public function stagiaire(){
        return $this->hasMany('App\Models\HrStagiaire','ecole_id');
    }
}
