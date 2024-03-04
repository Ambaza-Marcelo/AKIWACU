<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTakeCongePaye extends Model
{
    //
    protected $fillable = [
        'date_heure_debut',
        'date_heure_fin',
        'nbre_jours_conge_paye',
        'nbre_jours_conge_sollicite',
        'nbre_jours_conge_pris',
        'nbre_jours_conge_restant',
        'valide_par',
        'confirme_par',
        'approuve_par',
        'auteur',
        'etat',
        'employe_id'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }
}
