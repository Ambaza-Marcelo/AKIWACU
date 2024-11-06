<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialMeasurement extends Model
{
    //
    protected $fillable = [
        'purchase_unit',
        'stockout_unit',
    ];


    public function material(){
        return $this->hasMany('App\Models\Material','material_measurement_id');
    }
}
