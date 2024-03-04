<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkCategory extends Model
{
    //
    protected $fillable = ['name'];


    public function drink(){
        return $this->hasMany('App\Models\Drink','dcategory_id');
    }
}
