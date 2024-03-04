<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccompagnementDetail extends Model
{
    //
    protected $fillable = [
        'order_no',
        'order_signature',
        'employe_id',
        'food_item_id',
        'accompagnement_id'
    ];


    public function accompagnement(){
        return $this->belongsTo('App\Models\Accompagnement');
    }
}
