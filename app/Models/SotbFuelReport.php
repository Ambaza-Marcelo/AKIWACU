<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelReport extends Model
{
    //
    protected $fillable = [
        'date',
        'fuel_id',
        'quantite_stock_initiale',
        'valeur_stock_initiale',
        'quantity_stockin',
        'value_stockin',
        'stock_totale',
        'valeur_stock_totale',
        'quantity_stockout',
        'value_stockout',
        'quantite_stock_finale',
        'stockin_no',
        'stockout_no',
        'reception_no',
        'transaction',
        'auteur',
        'start_index',
        'end_index',
        'final_index',
        'pump_id',
        'car_id',
        'driver_id',
    ];

    public function car(){
        return $this->belongsTo('App\Models\SotbCar');
    }

    public function driver(){
        return $this->belongsTo('App\Models\SotbDriver');
    }

    public function pump(){
        return $this->belongsTo('App\Models\SotbFuelPump');
    }
    
    public function fuel(){
        return $this->belongsTo('App\Models\SotbFuel');
    }
}
