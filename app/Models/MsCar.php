<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsCar extends Model
{
    //
    protected $table = 'ms_cars';
    protected $fillable = [
        'marque',
        'couleur',
        'immatriculation',
        'chassis_no',
        'type',
        'etat',
        'auteur',
        'fuel_id',
    ];


    public function fuel(){
        return $this->belongsTo('App\Models\MsFuel');
    }

    public function requisition(){
        return $this->hasMany('App\Models\MsFuelRequisition','car_id');
    }

    public function requisitionDetail(){
        return $this->hasMany('App\Models\MsFuelRequisitionDetail','car_id');
    }
}
