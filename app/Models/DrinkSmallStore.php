<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkSmallStore extends Model
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
    public function drinkTransfer(){
        return $this->hasMany('App\Models\DrinkTransfer','destination_store_id');
    }

    public function drinkTransferDetail(){
        return $this->hasMany('App\Models\DrinkTransferDetail','destination_store_id');
    }
}
