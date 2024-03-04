<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarristStore extends Model
{
    //
    protected $fillable = [
        'quantity',
        'name',
        'code',
        'emplacement',
        'manager',
        'unit',
        'purchase_price',
        'selling_price',
        'cost_price',
        'cump',
        'total_value',
        'total_purchase_value',
        'total_selling_value',
        'total_cost_value',
        'total_cump_value',
        'threshold_quantity',
        'verified',
        'food_id',
        'drink_id',
        'created_by',
        'updated_by',
        'description',
    ];

    public function food(){
        return $this->belongsTo('App\Models\Food');
    }

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }
}
