<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbDriverCar extends Model
{
    //
    protected $table = 'sotb_driver_cars';
    protected $fillable = [
        'car_id',
        'driver_id',
    ];
}
