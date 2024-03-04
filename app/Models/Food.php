<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    //
    protected $table = 'foods';

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
    ];

    public function foodCategory(){
        return $this->belongsTo('App\Models\FoodCategory');
    }


    public function foodBigStore(){
        return $this->hasMany('App\Models\FoodBigStore','food_id');
    }

    public function foodItemDetail(){
        return $this->hasMany('App\Models\FoodItemDetail','food_id');
    }

    public function foodExtraBigStore(){
        return $this->hasMany('App\Models\FoodExtraBigStore','food_id');
    }

    public function foodSmallStore(){
        return $this->hasMany('App\Models\FoodSmallStore','food_id');
    }

    public function foodStockinDetail(){
        return $this->hasMany('App\Models\FoodStockinDetail','food_id');
    }

    public function foodStockoutDetail(){
        return $this->hasMany('App\Models\FoodStockoutDetail','food_id');
    }

    public function barristTransferDetail(){
        return $this->hasMany('App\Models\BarristTransferDetail','food_id');
    }

    public function foodBigStoreInventoryDetail(){
        return $this->hasMany('App\Models\FoodBigStoreInventoryDetail','food_id');
    }

    public function foodBigStoreDetail(){
        return $this->hasMany('App\Models\FoodBigStoreDetail','food_id');
    }

    public function foodSmallStoreDetail(){
        return $this->hasMany('App\Models\FoodSmallStoreDetail','food_id');
    }

    public function foodSmallStoreInventoryDetail(){
        return $this->hasMany('App\Models\FoodSmallStoreInventoryDetail','food_id');
    }

    public function foodReceptionDetail(){
        return $this->hasMany('App\Models\FoodReceptionDetail','food_id');
    }

    public function foodTransferDetail(){
        return $this->hasMany('App\Models\FoodTransferDetail','food_id');
    }

    public function foodBigReport(){
        return $this->hasMany('App\Models\FoodBigReport','food_id');
    }

    public function foodSmallReport(){
        return $this->hasMany('App\Models\FoodSmallReport','food_id');
    }

    public function barristBigReport(){
        return $this->hasMany('App\Models\BarristBigReport','food_id');
    }

    public function barristSmallReport(){
        return $this->hasMany('App\Models\BarristSmallReport','food_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\FactureDetail','food_id');
    }

    public function factureRestaurantDetail(){
        return $this->hasMany('App\Models\FactureRestaurantDetail','food_id');
    }
}
