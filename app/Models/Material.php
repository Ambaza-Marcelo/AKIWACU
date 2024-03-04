<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    //
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
        return $this->belongsTo('App\Models\FoodCategory');
    }


    public function materialBigStore(){
        return $this->hasMany('App\Models\MaterialBigStore','material_id');
    }

    public function materialExtraBigStore(){
        return $this->hasMany('App\Models\MaterialExtraBigStore','material_id');
    }

    public function materialSmallStore(){
        return $this->hasMany('App\Models\MaterialSmallStore','material_id');
    }

    public function stockinDetail(){
        return $this->hasMany('App\Models\StockinDetail','material_id');
    }

    public function materialStockoutDetail(){
        return $this->hasMany('App\Models\MaterialStockoutDetail','material_id');
    }

    public function materialBigStoreInventoryDetail(){
        return $this->hasMany('App\Models\MaterialBigStoreInventoryDetail','material_id');
    }

    public function materialBigStoreDetail(){
        return $this->hasMany('App\Models\MaterialBigStoreDetail','material_id');
    }

    public function materialSmallStoreDetail(){
        return $this->hasMany('App\Models\MaterialSmallStoreDetail','material_id');
    }

    public function materialSmallStoreInventoryDetail(){
        return $this->hasMany('App\Models\MaterialSmallStoreInventoryDetail','material_id');
    }

    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\MaterialReceptionDetail','material_id');
    }

    public function materialTransferDetail(){
        return $this->hasMany('App\Models\MaterialTransferDetail','material_id');
    }

    public function materialReturnDetail(){
        return $this->hasMany('App\Models\MaterialReturnDetail','material_id');
    }

    public function materialBigReport(){
        return $this->hasMany('App\Models\MaterialBigReport','material_id');
    }

    public function materialSmallReport(){
        return $this->hasMany('App\Models\MaterialSmallReport','material_id');
    }
}
