<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodSmallStore extends Model
{
    //
    protected $fillable = [
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
        return $this->belongsTo('food_id');
    }
}
