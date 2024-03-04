<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialSmallStore extends Model
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

    public function materialReturn(){
        return $this->hasMany('App\Models\MaterialReturn','origin_store_id');
    }

    public function materialReturnDetail(){
        return $this->hasMany('App\Models\MaterialReturnDetail','origin_store_id');
    }
}
