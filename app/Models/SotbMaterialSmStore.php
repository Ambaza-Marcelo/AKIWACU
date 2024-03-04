<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialSmStore extends Model
{
    //
    protected $table = 'sotb_material_sm_stores';
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
        return $this->hasMany('App\Models\SotbMaterialTransfer','origin_sm_store_id');
    }

    public function materialTransferDetail(){
        return $this->hasMany('App\Models\SotbMaterialTransferDetail','origin_sm_store_id');
    }

    public function materialReturn(){
        return $this->hasMany('App\Models\SotbMaterialReturn','destination_sm_store_id');
    }

    public function materialReturnDetail(){
        return $this->hasMany('App\Models\SotbMaterialReturnDetail','destination_sm_store_id');
    }

    public function materialReception(){
        return $this->hasMany('App\Models\SotbMaterialReception','destination_sm_store_id');
    }

    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\SotbMaterialReceptionDetail','destination_sm_store_id');
    }
}
