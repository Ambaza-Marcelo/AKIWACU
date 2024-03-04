<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsDriverCar extends Model
{
    //
    protected $table = 'ms_driver_cars';
    protected $fillable = [
        'car_id',
        'driver_id',
    ];
}
