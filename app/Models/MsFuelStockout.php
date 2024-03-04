<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFuelStockout extends Model
{
    //
    protected $table = 'ms_fuel_stockouts';
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
        return $this->belongsTo('App\Models\MsFuelPump');
    }

    public function car(){
        return $this->belongsTo('App\Models\MsCar');
    }

    public function driver(){
        return $this->belongsTo('App\Models\MsDriver');
    }

    public function fuel(){
        return $this->belongsTo('App\Models\MsFuel');
    }

}
