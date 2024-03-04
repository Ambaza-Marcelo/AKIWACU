<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelRequisition extends Model
{
    //
    protected $table = 'sotb_fuel_requisitions';
    protected $fillable = [
        'date',
        'requisition_no',
        'requisition_signature',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
    ];

    public function fuel(){
        return $this->belongsTo('App\Models\SotbFuel');
    }
}
