<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateStoreItem extends Model
{
    //
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
    ];

    public function privateDrinkStockinDetail(){
        return $this->hasMany('App\Models\PrivateDrinkStockinDetail','private_store_item_id');
    }

    public function privateDrinkStockoutDetail(){
        return $this->hasMany('App\Models\PrivateDrinkStockoutDetail','private_store_item_id');
    }
public function privateDrinkInventoryDetail(){
        return $this->hasMany('App\Models\PrivateDrinkInventoryDetail','private_store_item_id');
    }
}
