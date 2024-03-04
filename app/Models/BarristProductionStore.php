<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarristProductionStore extends Model
{
    //
    protected $fillable = [
        'quantity',
        'quantity_food',
        'quantity_drink',
        'name',
        'code',
        'emplacement',
        'manager',
        'unit',
        'unit_food',
        'unit_drink',
        'purchase_price',
        'selling_price',
        'cost_price',
        'cump',
        'total_value',
        'total_purchase_value',
        'total_selling_value',
        'total_cost_value',
        'total_cump_value',
        'threshold_quantity',
        'verified',
        'barrist_item_id',
        'created_by',
        'updated_by',
        'description',
    ];

    public function barristItem(){
        return $this->belongsTo('App\Models\barristItem');
    }
}
