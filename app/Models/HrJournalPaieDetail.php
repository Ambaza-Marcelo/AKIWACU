<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrJournalPaieDetail extends Model
{
    //
    protected $table = 'hr_journal_paie_details';
    protected $fillable = [
        'children_number',
        'statut_matrimonial',
        'departement_code',
        'date_debut',
        'date_fin',
        'etat',
        'code',
        'somme_salaire_base',
        'somme_salaire_net',
        'nbre_jours_ouvrables',
        'nbre_jours_conges',
        'nbre_jours_conges_consomes',
        'code_banque',
        'numero_compte',
        'designation_ind_imposable',
        'montant_ind_imposable',
        'designation_ind_non_imposable',
        'montant_ind_non_imposable',
        'designation_prime_non_imposable',
        'montant_prime_non_imposable',
        'designation_prime_imposable',
        'montant_prime_imposable',
        'taux_assurance_maladie',
        'taux_retraite',
        'interet_credit_logement',
        'retenue_pret',
        'autre_retenue',
        'created_by',
        'nbre_jours_conge_pris',
        'nbre_jours_absences',
        'employe_id',
        'indemnite_id',
        'cotisation_id',
        'take_conge_id'
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
