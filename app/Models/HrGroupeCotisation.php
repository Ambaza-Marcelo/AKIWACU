<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrGroupeCotisation extends Model
{
    //
    protected $table = 'hr_groupe_cotisations';
    protected $fillable = [
        'designation',
        'classe_inferieure',
        'employe_id',
        'classe_superieure'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }

    public function cotisation(){
        return $this->hasMany('App\Models\HrCotisation','groupe_cotisation_id');
    }
}
