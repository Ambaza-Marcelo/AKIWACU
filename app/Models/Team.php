<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    //
    protected $fillable = [
        'image',
        'name',
        'description',
        'position_id'
    ];

    public function position(){
        return $this->belongsTo('App\Models\Position');
    }
}
