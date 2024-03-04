<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterial extends Model
{
    //
    protected $table = 'ms_materials';
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
        return $this->belongsTo('App\Models\MsMaterialCategory');
    }

    public function stockinDetail(){
        return $this->hasMany('App\Models\MsMaterialStockinDetail','material_id');
    }

    public function materialStockoutDetail(){
        return $this->hasMany('App\Models\MsMaterialStockoutDetail','material_id');
    }

    public function materialStoreInventoryDetail(){
        return $this->hasMany('App\Models\MsMaterialStoreInventoryDetail','material_id');
    }

    public function materialStoreDetail(){
        return $this->hasMany('App\Models\MsMaterialStoreDetail','material_id');
    }

    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\MsMaterialReceptionDetail','material_id');
    }

    public function materialRequisitionDetail(){
        return $this->hasMany('App\Models\MsMaterialRequisitionDetail','material_id');
    }

    public function materialStoreReport(){
        return $this->hasMany('App\Models\MsMaterialReport','material_id');
    }

}
