<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkMeasurement extends Model
{
    //
    protected $fillable = [
        'purchase_unit',
        'stockout_unit',
        'production_unit',
        'equivalent'
    ];


    public function drink(){
        return $this->hasMany('App\Models\Drink','drink_measurement_id');
    }
}
