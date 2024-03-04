<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrImpot extends Model
{
    //
    protected $fillable = [
        'pourcentage_impot',
        'groupe_impot_id'
    ];


    public function groupeImpot(){
        return $this->belongsTo('App\Models\HrGroupeImpot');
    }

    public function paiement(){
        return $this->hasMany('App\Models\HrPaiement','impot_id');
    }

    public function report(){
        return $this->hasMany('App\Models\HrReport','impot_id');
    }

    public function journalPaieDetail(){
        return $this->hasMany('App\Models\HrJournalPaieDetail','impot_id');
    }
}
