<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelStockoutDetail extends Model
{
    //
    protected $table = 'sotb_fuel_stockout_details';
    protected $fillable = [
        'fuel_id',
        'date',
        'quantity',
        'unit',
        'price',
        'stockout_no',
        'stockout_signature',
        'requisition_no',
        'description',
        'rejected_motif',
        'total_value',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'asker',
        'destination',
        'status',
        'type_pump',
        'pump_id',
        'car_id',
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
