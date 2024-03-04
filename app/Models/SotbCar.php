<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbCar extends Model
{
    //
    protected $table = 'sotb_cars';
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
        return $this->belongsTo('App\Models\SotbFuel');
    }

    public function requisition(){
        return $this->hasMany('App\Models\SotbFuelRequisition','car_id');
    }

    public function requisitionDetail(){
        return $this->hasMany('App\Models\SotbFuelRequisitionDetail','car_id');
    }
}
