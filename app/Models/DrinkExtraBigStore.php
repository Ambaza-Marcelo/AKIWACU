<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkExtraBigStore extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'store_signature',
        'emplacement',
        'manager',
        'created_by',
        'updated_by',
        'description',
    ];

    public function drinkTransfer(){
        return $this->hasMany('App\Models\DrinkTransfer','origin_extra_store_id');
    }

    public function drinkTransferDetail(){
        return $this->hasMany('App\Models\DrinkTransferDetail','origin_extra_store_id');
    }

    public function drinkStockout(){
        return $this->hasMany('App\Models\DrinkStockout','origin_extra_store_id');
    }

    public function drinkReception(){
        return $this->hasMany('App\Models\DrinkReception','destination_bg_store_id');
    }

    public function drinkReceptionDetail(){
        return $this->hasMany('App\Models\DrinkReceptionDetail','destination_bg_store_id');
    }
}
