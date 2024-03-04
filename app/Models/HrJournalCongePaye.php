<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrJournalCongePaye extends Model
{
    //
    protected $fillable = [
        'etat',
        'session',
        'date_heure_debut',
        'date_heure_fin',
        'nbre_jours_conge_paye',
        'nbre_jours_conge_sollicite',
        'nbre_jours_conge_pris',
        'nbre_jours_conge_restant',
        'valide_par',
        'confirme_par',
        'approuve_par',
        'created_by',
        'employe_id'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }
}
