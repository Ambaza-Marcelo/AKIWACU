<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFuel extends Model
{
    //
    protected $table = 'ms_fuels';
    protected $fillable = [
        'name',
        'quantity',
        'purchase_price',
        'cump',
        'cost_price',
        'auteur',
    ];

    public function car(){
        return $this->hasMany('App\Models\MsCar','fuel_id');
    }

    public function pump(){
        return $this->hasMany('App\Models\MsFuelPump','fuel_id');
    }

    public function requisition(){
        return $this->hasMany('App\Models\MsFuelRequisition','fuel_id');
    }

    public function requisitionDetail(){
        return $this->hasMany('App\Models\MsFuelRequisitionDetail','fuel_id');
    }

    public function stockout(){
        return $this->hasMany('App\Models\MsFuelStockout','fuel_id');
    }

    public function stockoutDetail(){
        return $this->hasMany('App\Models\MsFuelStockoutDetail','fuel_id');
    }

    public function stockinDetail(){
        return $this->hasMany('App\Models\MsFuelStockinDetail','fuel_id');
    }
}
