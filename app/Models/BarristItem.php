<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarristItem extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'specification',
        'code_store',
        'quantity',
        'unit',
        'purchase_price',
        'cost_price',
        'selling_price',
        'cump',
        'threshold_quantity',
        'expiration_date',
        'status',
        'store_type',
        'updated_by',
        'created_by',
    ];

    public function barristProductionStore(){
        return $this->hasMany('App\Models\BarristProductionStore','barrist_item_id');
    }

    public function barristOrderDetail(){
        return $this->hasMany('App\Models\BarristOrderDetail','barrist_item_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\FactureDetail','barrist_item_id');
    }
}
