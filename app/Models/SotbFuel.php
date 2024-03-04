<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuel extends Model
{
    //
    protected $table = 'sotb_fuels';
    protected $fillable = [
        'name',
        'quantity',
        'purchase_price',
        'cump',
        'cost_price',
        'auteur',
    ];

    public function car(){
        return $this->hasMany('App\Models\SotbCar','fuel_id');
    }

    public function pump(){
        return $this->hasMany('App\Models\SotbFuelPump','fuel_id');
    }

    public function requisition(){
        return $this->hasMany('App\Models\SotbFuelRequisition','fuel_id');
    }

    public function requisitionDetail(){
        return $this->hasMany('App\Models\SotbFuelRequisitionDetail','fuel_id');
    }

    public function stockout(){
        return $this->hasMany('App\Models\SotbFuelStockout','fuel_id');
    }

    public function stockoutDetail(){
        return $this->hasMany('App\Models\SotbFuelStockoutDetail','fuel_id');
    }

    public function stockinDetail(){
        return $this->hasMany('App\Models\SotbFuelStockinDetail','fuel_id');
    }
}
