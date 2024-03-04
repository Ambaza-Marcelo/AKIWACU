<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkSupplierOrderDetail extends Model
{
    //
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
        'drink_id',
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\Supplier');
    }

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }
}
