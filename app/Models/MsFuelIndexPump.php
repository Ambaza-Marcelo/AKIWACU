<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFuelIndexPump extends Model
{
    //
    protected $table = 'ms_fuel_index_pumps';
    protected $fillable = [
        'start_index',
        'end_index',
        'date',
        'final_index',
        'auteur',
    ];
}
