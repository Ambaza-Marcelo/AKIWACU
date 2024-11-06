<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodMeasurement extends Model
{
    //
    protected $fillable = [
        'purchase_unit',
        'stockout_unit',
        'production_unit',
        'equivalent'
    ];


    public function food(){
        return $this->hasMany('App\Models\Food','food_measurement_id');
    }
}
