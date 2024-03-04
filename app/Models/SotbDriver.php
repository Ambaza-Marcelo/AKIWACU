<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbDriver extends Model
{
    //
    protected $table = 'sotb_drivers';
    protected $fillable = [
        'firstname',
        'lastname',
        'telephone',
        'email',
        'gender',
    ];

    public function requisition(){
        return $this->hasMany('App\Models\SotbFuelRequisition','driver_id');
    }

    public function requisitionDetail(){
        return $this->hasMany('App\Models\SotbFuelRequisitionDetail','driver_id');
    }
}
