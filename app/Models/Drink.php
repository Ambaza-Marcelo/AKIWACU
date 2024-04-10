<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'specification',
        'quantity_bottle',
        'quantity_ml',
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
        'dcategory_id',
    ];

    public function drinkCategory(){
        return $this->belongsTo('App\Models\DrinkCategory');
    }

    public function drinkBigStore(){
        return $this->hasMany('App\Models\DrinkBigStore','drink_id');
    }

    public function drinkExtraBigStore(){
        return $this->hasMany('App\Models\DrinkExtraBigStore','drink_id');
    }

    public function drinkSmallStore(){
        return $this->hasMany('App\Models\DrinkSmallStore','drink_id');
    }

    public function drinkStockinDetail(){
        return $this->hasMany('App\Models\DrinkStockinDetail','drink_id');
    }

    public function virtualDrinkMdStore(){
        return $this->hasMany('App\Models\VirtualDrinkMdStore','drink_id');
    }

    public function virtualDrinkSmStore(){
        return $this->hasMany('App\Models\VirtualDrinkSmStore','drink_id');
    }

    public function drinkStockoutDetail(){
        return $this->hasMany('App\Models\DrinkStockoutDetail','drink_id');
    }

    public function barristTransferDetail(){
        return $this->hasMany('App\Models\BarristTransferDetail','drink_id');
    }

    public function drinkBigStoreInventoryDetail(){
        return $this->hasMany('App\Models\DrinkBigStoreInventoryDetail','drink_id');
    }

    public function drinkBigStoreDetail(){
        return $this->hasMany('App\Models\DrinkBigStoreDetail','drink_id');
    }

    public function drinkSmallStoreInventoryDetail(){
        return $this->hasMany('App\Models\DrinkSmallStoreInventoryDetail','drink_id');
    }

    public function drinkReceptionDetail(){
        return $this->hasMany('App\Models\DrinkReceptionDetail','drink_id');
    }

    public function drinkTransferDetail(){
        return $this->hasMany('App\Models\DrinkTransferDetail','drink_id');
    }

    public function drinkBigReport(){
        return $this->hasMany('App\Models\DrinkBigReport','drink_id');
    }

    public function drinkSmallReport(){
        return $this->hasMany('App\Models\DrinkSmallReport','drink_id');
    }

    public function barristBigReport(){
        return $this->hasMany('App\Models\BarristBigReport','drink_id');
    }

    public function barristSmallReport(){
        return $this->hasMany('App\Models\BarristSmallReport','drink_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\FactureDetail','drink_id');
    }

    public function orderDrinkDetail(){
        return $this->hasMany('App\Models\OrderDrinkDetail','drink_id');
    }
}
