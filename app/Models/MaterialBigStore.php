<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialBigStore extends Model
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

    public function materialTransfer(){
        return $this->hasMany('App\Models\MaterialTransfer','origin_store_id');
    }

    public function materialTransferDetail(){
        return $this->hasMany('App\Models\MaterialTransferDetail','origin_store_id');
    }

    public function materialReturn(){
        return $this->hasMany('App\Models\MaterialReturn','destination_store_id');
    }

    public function materialReturnDetail(){
        return $this->hasMany('App\Models\MaterialReturnDetail','destination_store_id');
    }

    public function materialReception(){
        return $this->hasMany('App\Models\MaterialReception','destination_store_id');
    }

    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\MaterialReceptionDetail','destination_store_id');
    }
}
