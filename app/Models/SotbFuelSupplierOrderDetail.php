<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelSupplierOrderDetail extends Model
{
    //
    protected $table = 'sotb_fuel_supplier_order_details';
    protected $fillable = [
        'date',
        'quantity',
        'unit',
        'purchase_price',
        'order_no',
        'order_signature',
        'description',
        'rejected_motif',
        'total_value',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'supplier_id',
        'fuel_id',
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\SotbSupplier');
    }

    public function fuel(){
        return $this->belongsTo('App\Models\SotbFuel');
    }
}
