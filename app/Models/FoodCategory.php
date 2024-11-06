<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodCategory extends Model
{
    //
    protected $fillable = ['name'];


    public function food(){
        return $this->hasMany('App\Models\Food','fcategory_id');
    }
}
