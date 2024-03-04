<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkSmallStoreDetail extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'store_signature',
        'emplacement',
        'manager',
        'quantity_bottle',
        'unit',
        'total_value_bottle',
        'quantity_ml',
        'total_value_ml',
        'threshold_quantity',
        'verified',
        'drink_id',
        'created_by',
        'updated_by',
        'description',
        'purchase_price',
        'selling_price',
        'cost_price',
        'cump',
        'total_purchase_value',
        'total_selling_value',
        'total_cost_value',
        'total_cump_value',
    ];

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }
}
