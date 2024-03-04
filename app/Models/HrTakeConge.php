<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTakeConge extends Model
{
    //
    protected $fillable = [
        'date_heure_debut',
        'date_heure_fin',
        'nbre_jours_conge_pris',
        'nbre_heures_conge_pris',
        'valide_par',
        'confirme_par',
        'approuve_par',
        'type_conge_id',
        'employe_id',
        'stagiaire_id'
    ];

    public function typeConge(){
        return $this->belongsTo('App\Models\HrTypeConge');
    }

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }

    public function stagiaire(){
        return $this->belongsTo('App\Models\HrStagiaire');
    }

    public function paiement(){
        return $this->hasMany('App\Models\HrPaiement','take_conge_id');
    }

    public function report(){
        return $this->hasMany('App\Models\HrReport','take_conge_id');
    }
}
