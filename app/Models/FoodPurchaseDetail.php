<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodPurchaseDetail extends Model
{
    //
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
        'food_id',
        'supplier_id'
    ];

    public function food(){
        return $this->belongsTo('App\Models\Food');
    }

    public function supplier(){
        return $this->belongsTo('App\Models\Supplier');
    }
}
