<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelSupplierOrder extends Model
{
    //
    protected $table = 'sotb_fuel_supplier_orders';
    protected $fillable = [
        'date',
        'order_no',
        'order_signature',
        'description',
        'rejected_motif',
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
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\SotbSupplier');
    }
}
