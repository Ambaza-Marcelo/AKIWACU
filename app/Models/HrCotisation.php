<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrCotisation extends Model
{
    //
    protected $fillable = [
        'pourcentage_sb',
        'groupe_cotisation_id',
        'type_cotisation_id'
    ];

    public function typeCotisation(){
        return $this->belongsTo('App\Models\HrTypeCotisation');
    }

    public function groupeCotisation(){
        return $this->belongsTo('App\Models\HrGroupeCotisation');
    }

    public function paiement(){
        return $this->hasMany('App\Models\HrPaiement','cotisation_id');
    }

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }

    public function report(){
        return $this->hasMany('App\Models\HrReport','cotisation_id');
    }

    public function journalPaieDetail(){
        return $this->hasMany('App\Models\HrJournalPaieDetail','cotisation_id');
    }
}
