<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItemDetail extends Model
{
    //
    protected $table = 'food_item_details';

    protected $fillable = [
        'name',
        'code',
        'specification',
        'quantity',
        'unit',
        'purchase_price',
        'cost_price',
        'selling_price',
        'cump',
        'threshold_quantity',
        'expiration_date',
        'status',
        'updated_by',
        'created_by',
        'fcategory_id',
        'food_id'
    ];

    public function foodCategory(){
        return $this->belongsTo('App\Models\FoodCategory');
    }

    public function food(){
        return $this->belongsTo('App\Models\Food');
    }

    public function foodStore(){
        return $this->hasMany('App\Models\FoodStore','food_item_id');
    }

    public function facture(){
        return $this->hasMany('App\Models\FactureDetail','food_item_id');
    }

    public function orderKitchenDetail(){
        return $this->hasMany('App\Models\OrderKitchenDetail','food_item_id');
    }
}
