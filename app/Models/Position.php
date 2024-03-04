<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    //
    protected $fillable = [
        'name'
    ];

    public function team(){
        return $this->hasMany('App\Models\Position','position_id');
    }
}
