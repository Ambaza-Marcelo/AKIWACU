<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrIndemnite extends Model
{
    //
    protected $fillable = [
        'pourcentage_sb',
        'contrat_id',
        'type_indemnite_id',
        'groupe_indemnite_id'
    ];

    public function typeIndemnite(){
        return $this->belongsTo('App\Models\HrTypeIndemnite');
    }

    public function groupeIndemnite(){
        return $this->belongsTo('App\Models\HrGroupeIndemnite');
    }

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }

    public function paiement(){
        return $this->hasMany('App\Models\HrPaiement','indemnite_id');
    }

    public function report(){
        return $this->hasMany('App\Models\HrReport','indemnite_id');
    }

    public function journalPaieDetail(){
        return $this->hasMany('App\Models\HrJournalPaieDetail','indemnite_id');
    }
}
