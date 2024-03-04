<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkBigStore extends Model
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
        return $this->hasMany('App\Models\DrinkTransfer','origin_store_id');
    }

    public function drinkTransferDetail(){
        return $this->hasMany('App\Models\DrinkTransferDetail','origin_store_id');
    }

    public function barristTransfer(){
        return $this->hasMany('App\Models\BarristTransfer','origin_dstore_id');
    }

    public function barristTransferDetail(){
        return $this->hasMany('App\Models\BarristTransferDetail','origin_dstore_id');
    }

    public function drinkReception(){
        return $this->hasMany('App\Models\DrinkReception','destination_store_id');
    }

    public function drinkReceptionDetail(){
        return $this->hasMany('App\Models\DrinkReceptionDetail','destination_store_id');
    }
}
