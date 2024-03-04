<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accompagnement extends Model
{
    //
    protected $fillable = ['name'];


    public function orderKitchenDetail(){
        return $this->hasMany('App\Models\OrderKitchenDetail','accompagnement_id');
    }

    public function accompagnementDetail(){
        return $this->hasMany('App\Models\AccompagnementDetail','accompagnement_id');
    }
}
