<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrImpotDetail extends Model
{
    //
    protected $table = 'hr_impot_details';
    protected $fillable = [
        'date_debut',
        'date_fin',
        'etat',
        'code',
        'mois',
        'annee',
        'employe_id',
        'indemnite_id',
        'cotisation_id',
        'take_conge_id',
        'statut_matrimonial',
        'children_number',
        'taux_assurance_maladie',
        'taux_retraite',
        'interet_credit_logement',
        'retenue_pret',
        'base_imposable',
        'impot_preleve',
        'impot_sur_salaire',
        'somme_salaire_brut_imposable',
        'somme_salaire_brut_non_imposable',
        'somme_salaire_net_imposable',
        'somme_salaire_net_non_imposable',
        'code_banque',
        'code_departement',
        'numero_compte',
        'designation_ind_imposable',
        'montant_ind_imposable',
        'designation_ind_non_imposable',
        'montant_ind_non_imposable',
        'designation_prime_non_imposable',
        'montant_prime_non_imposable',
        'designation_prime_imposable',
        'montant_prime_imposable',
        'nbre_jours_conge_pris',
        'nbre_personnes_a_charge',
        'nbre_jours_ouvrables',
        'autre_retenue',
        'total_retenue',
        'nbre_jours_prestes',
        'avance_sur_salaire'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }

    public function indemnite(){
        return $this->belongsTo('App\Models\HrIndemnite');
    }

    public function cotisation(){
        return $this->belongsTo('App\Models\HrCotisation');
    }

    public function takeConge(){
        return $this->belongsTo('App\Models\HrTakeConge');
    }

    public function takeCongePaye(){
        return $this->belongsTo('App\Models\HrTakeCongePaye');
    }
}
