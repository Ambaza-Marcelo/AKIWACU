<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodExtraBigStore extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'emplacement',
        'manager',
        'created_by',
        'updated_by',
        'description',
    ];

    public function foodTransfer(){
        return $this->hasMany('App\Models\FoodTransfer','origin_store_id');
    }

    public function foodTransferDetail(){
        return $this->hasMany('App\Models\FoodTransferDetail','origin_store_id');
    }

    public function foodReception(){
        return $this->hasMany('App\Models\FoodReception','destination_store_id');
    }

    public function foodReceptionDetail(){
        return $this->hasMany('App\Models\FoodReceptionDetail','destination_store_id');
    }
}
