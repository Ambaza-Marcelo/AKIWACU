<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialBgStore extends Model
{
    //
    protected $table = 'sotb_material_bg_stores';
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
        return $this->hasMany('App\Models\SotbMaterialTransfer','origin_bg_store_id');
    }

    public function materialTransferDetail(){
        return $this->hasMany('App\Models\SotbMaterialTransferDetail','origin_bg_store_id');
    }

    public function materialReturn(){
        return $this->hasMany('App\Models\SotbMaterialReturn','destination_bg_store_id');
    }

    public function materialReturnDetail(){
        return $this->hasMany('App\Models\SotbMaterialReturnDetail','destination_bg_store_id');
    }

    public function materialReception(){
        return $this->hasMany('App\Models\SotbMaterialReception','destination_bg_store_id');
    }

    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\SotbMaterialReceptionDetail','destination_bg_store_id');
    }
}
