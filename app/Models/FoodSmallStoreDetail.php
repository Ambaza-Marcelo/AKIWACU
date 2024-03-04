<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodSmallStoreDetail extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'store_signature',
        'emplacement',
        'manager',
        'quantity',
        'unit',
        'total_value',
        'threshold_quantity',
        'verified',
        'food_id',
        'created_by',
        'updated_by',
        'description',
    ];

    public function food(){
        return $this->belongsTo('App\Models\Food');
    }
}
