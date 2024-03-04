<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrEmploye extends Model
{
    //
     protected $fillable = [
        'firstname',
        'lastname',
        'phone_no',
        'mail',
        'matricule_no',
        'fathername',
        'mothername',
        'cni',
        'birthdate',
        'bloodgroup',
        'province',
        'commune',
        'zone',
        'quartier',
        'gender',
        'children_number',
        'province_residence_actuel',
        'commune_residence_actuel',
        'zone_residence_actuel',
        'quartier_residence_actuel',
        'avenue_residence_actuel',
        'numero',
        'image',
        'pays',
        'statut_matrimonial',
        'departement_code',
        'document',
        'date_debut',
        'date_fin',
        'etat',
        'somme_salaire_base',
        'somme_salaire_net',
        'nbre_jours_ouvrables',
        'nbre_jours_conges',
        'nbre_jours_conges_consomes',
        'code_banque',
        'numero_compte',
        'designation_ind_imposable',
        'montant_ind_imposable',
        'indemnite_deplacement',
        'montant_ind_non_imposable',
        'indemnite_logement',
        'prime_fonction',
        'taux_assurance_maladie',
        'taux_retraite',
        'interet_credit_logement',
        'retenue_pret',
        'autre_retenue',
        'created_by',
        'departement_id',
        'service_id',
        'fonction_id',
        'grade_id',
        'banque_id',
    ];

    public function departement(){
        return $this->belongsTo('App\Models\HrDepartement');
    }

    public function service(){
        return $this->belongsTo('App\Models\HrService');
    }

    public function banque(){
        return $this->belongsTo('App\Models\HrBanque');
    }

    public function grade(){
        return $this->belongsTo('App\Models\HrGrade');
    }

    public function fonction(){
        return $this->belongsTo('App\Models\HrFonction');
    }

    public function employe(){
        return $this->hasMany('App\Models\HrEmploye','employe_id');
    }

    public function cotation(){
        return $this->hasMany('App\Models\HrCotation','employe_id');
    }

    public function report(){
        return $this->hasMany('App\Models\HrReport','employe_id');
    }

    public function takeConge(){
        return $this->hasMany('App\Models\HrTakeConge','employe_id');
    }

    public function paiement(){
        return $this->hasMany('App\Models\HrPaiement','employe_id');
    }
}
