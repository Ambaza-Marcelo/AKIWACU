<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelIndexPump extends Model
{
    //
    protected $table = 'sotb_fuel_index_pumps';
    protected $fillable = [
        'start_index',
        'end_index',
        'date',
        'final_index',
        'auteur',
    ];
}
