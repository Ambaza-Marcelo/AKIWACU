<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrCongePaye extends Model
{
    //
    protected $fillable = [
        'session',
        'nbre_jours',
        'created_by',
        'employe_id'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }
}
