<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderKitchenDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'order_no',
        'order_signature',
        'quantity',
        'total_amount_purchase',
        'total_amount_selling',
        'purchase_price',
        'selling_price',
        'employe_id',
        'status',
        'table_no',
        'created_by',
        'description',
        'employe_id',
        'food_item_id',
        'accompagnement_id',
        'table_id'

    ];

    public function employe(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function foodItem(){
        return $this->belongsTo('App\Models\FoodItem');
    }

    public function accompagnement(){
        return $this->belongsTo('App\Models\Accompagnement');
    }

    public function table(){
        return $this->belongsTo('App\Models\Table');
    }

}
