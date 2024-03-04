<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelStockout extends Model
{
    //
    protected $table = 'sotb_fuel_stockouts';
    protected $fillable = [
        'date',
        'stockout_no',
        'stockout_signature',
        'requisition_no',
        'asker',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'asker',
        'destination',
        'status',
        'type_pump',
        'pump_id',
        'car_id',
        'fuel_id',
        'driver_id'
    ];

    public function pump(){
        return $this->belongsTo('App\Models\SotbFuelPump');
    }

    public function car(){
        return $this->belongsTo('App\Models\SotbCar');
    }

    public function driver(){
        return $this->belongsTo('App\Models\SotbDriver');
    }

    public function fuel(){
        return $this->belongsTo('App\Models\SotbFuel');
    }

}
