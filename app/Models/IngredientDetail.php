<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientDetail extends Model
{
    //
    protected $fillable = [
        'order_no',
        'order_signature',
        'employe_id',
        'food_item_id',
        'ingredient_id'
    ];


    public function ingredient(){
        return $this->belongsTo('App\Models\Ingredient');
    }
}
