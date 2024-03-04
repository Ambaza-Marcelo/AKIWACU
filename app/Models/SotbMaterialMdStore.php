<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialMdStore extends Model
{
    //
    protected $table = 'sotb_material_md_stores';
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
        return $this->hasMany('App\Models\SotbMaterialTransfer','origin_md_store_id');
    }

    public function materialTransferDetail(){
        return $this->hasMany('App\Models\SotbMaterialTransferDetail','origin_md_store_id');
    }

    public function materialReturn(){
        return $this->hasMany('App\Models\SotbMaterialReturn','destination_md_store_id');
    }

    public function materialReturnDetail(){
        return $this->hasMany('App\Models\SotbMaterialReturnDetail','destination_md_store_id');
    }

    public function materialReception(){
        return $this->hasMany('App\Models\SotbMaterialReception','destination_md_store_id');
    }

    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\SotbMaterialReceptionDetail','destination_md_store_id');
    }
}
