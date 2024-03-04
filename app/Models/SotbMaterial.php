<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterial extends Model
{
    //
    protected $table = 'sotb_materials';
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
        'mcategory_id',
    ];

    public function materialCategory(){
        return $this->belongsTo('App\Models\SotbMaterialCategory');
    }


    public function materialBgStore(){
        return $this->hasMany('App\Models\SotbMaterialBgStore','material_id');
    }

    public function materialMdStore(){
        return $this->hasMany('App\Models\SotbMaterialMdStore','material_id');
    }

    public function materialSmStore(){
        return $this->hasMany('App\Models\SotbMaterialSmStore','material_id');
    }

    public function stockinDetail(){
        return $this->hasMany('App\Models\SotbMaterialStockinDetail','material_id');
    }

    public function materialStockoutDetail(){
        return $this->hasMany('App\Models\SotbMaterialStockoutDetail','material_id');
    }

    public function materialBgStoreInventoryDetail(){
        return $this->hasMany('App\Models\SotbMaterialBgStoreInventoryDetail','material_id');
    }

    public function materialBgStoreDetail(){
        return $this->hasMany('App\Models\SotbMaterialBgStoreDetail','material_id');
    }

    public function materialSmStoreDetail(){
        return $this->hasMany('App\Models\SotbMaterialSmStoreDetail','material_id');
    }

    public function materialSmStoreInventoryDetail(){
        return $this->hasMany('App\Models\SotbMaterialSmStoreInventoryDetail','material_id');
    }

    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\SotbMaterialReceptionDetail','material_id');
    }

    public function materialTransferDetail(){
        return $this->hasMany('App\Models\SotbMaterialTransferDetail','material_id');
    }

    public function materialRequisitionDetail(){
        return $this->hasMany('App\Models\SotbMaterialRequisitionDetail','material_id');
    }

    public function materialReturnDetail(){
        return $this->hasMany('App\Models\SotbMaterialReturnDetail','material_id');
    }

    public function materialBgStoreReport(){
        return $this->hasMany('App\Models\SotbMaterialBgReport','material_id');
    }

    public function materialMdStoreReport(){
        return $this->hasMany('App\Models\SotbMaterialMdReport','material_id');
    }

    public function materialSmStoreReport(){
        return $this->hasMany('App\Models\SotbMaterialSmReport','material_id');
    }
}
