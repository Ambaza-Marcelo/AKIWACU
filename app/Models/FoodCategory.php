<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodCategory extends Model
{
    //
    protected $fillable = ['name'];


    public function foodCategory(){
        return $this->hasMany('App\Models\FoodCategory','fcategory_id');
    }
}
