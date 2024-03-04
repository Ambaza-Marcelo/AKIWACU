<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrGroupeIndemnite extends Model
{
    //
    protected $fillable = [
        'designation',
        'classe_inferieure',
        'contrat_id',
        'classe_superieure'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }

    public function indemnite(){
        return $this->hasMany('App\Models\HrIndemnite','groupe_indemnite_id');
    }
}
