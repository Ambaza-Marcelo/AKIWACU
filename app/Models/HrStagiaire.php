<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrStagiaire extends Model
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
        'status',
        'children_number',
        'province_residence_actuel',
        'commune_residence_actuel',
        'zone_residence_actuel',
        'quartier_residence_actuel',
        'avenue_residence_actuel',
        'numero',
        'departement_id',
        'service_id',
        'fonction_id',
        'grade_id',
        'ecole_id',
        'filiere_id'
    ];

    public function departement(){
        return $this->belongsTo('App\Models\HrDepartement');
    }

    public function service(){
        return $this->belongsTo('App\Models\HrService');
    }

    public function grade(){
        return $this->belongsTo('App\Models\HrGrade');
    }

    public function fonction(){
        return $this->belongsTo('App\Models\HrFonction');
    }

    public function ecole(){
        return $this->belongsTo('App\Models\HrEcole');
    }

    public function filiere(){
        return $this->belongsTo('App\Models\HrFiliere');
    }

    public function takeConge(){
        return $this->hasMany('App\Models\HrTakeConge','stagiaire_id');
    }
}
