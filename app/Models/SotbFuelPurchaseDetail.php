<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelPurchaseDetail extends Model
{
    //
    protected $table = 'sotb_fuel_purchase_details';
    protected $fillable = [
        'date',
        'quantity',
        'unit',
        'price',
        'purchase_no',
        'purchase_signature',
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
        'fuel_id',
    ];

    public function fuel(){
        return $this->belongsTo('App\Models\SotbFuel');
    }
}
