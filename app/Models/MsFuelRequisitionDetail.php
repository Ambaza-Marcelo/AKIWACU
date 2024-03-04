<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFuelRequisitionDetail extends Model
{
    //
    protected $table = 'ms_fuel_requisition_details';
    protected $fillable = [
        'date',
        'quantity_requisitioned',
        'quantity_received',
        'unit',
        'price',
        'requisition_no',
        'requisition_signature',
        'description',
        'rejected_motif',
        'total_value_requisitioned',
        'total_value_received',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'fuel_id',
        'driver_id',
        'car_id'
    ];

    public function fuel(){
        return $this->belongsTo('App\Models\MsFuel');
    }

    public function driver(){
        return $this->belongsTo('App\Models\MsDriver');
    }

    public function car(){
        return $this->belongsTo('App\Models\MsCar');
    }
}
