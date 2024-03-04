<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    //
    protected $fillable = ['name'];


    public function barristOrderDetail(){
        return $this->hasMany('App\Models\BarristOrderDetail','accompagnement_id');
    }

    public function ingredientDetail(){
        return $this->hasMany('App\Models\IngredientDetail','ingredient_id');
    }
}
