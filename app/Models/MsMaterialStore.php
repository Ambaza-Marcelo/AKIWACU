<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialStore extends Model
{
    //
    protected $table = 'ms_material_stores';
    protected $fillable = [
        'name',
        'code',
        'emplacement',
        'manager',
        'created_by',
        'updated_by',
        'description',
    ];


    public function materialStockin(){
        return $this->hasMany('App\Models\MsMaterialStockin','destination_store_id');
    }

    public function materialStockinDetail(){
        return $this->hasMany('App\Models\MsMaterialStockinDetail','destination_store_id');
    }

    public function materialStockout(){
        return $this->hasMany('App\Models\MsMaterialStockout','destination_store_id');
    }

    public function materialStockoutDetail(){
        return $this->hasMany('App\Models\MsMaterialStockoutDetail','destination_store_id');
    }

    public function materialReception(){
        return $this->hasMany('App\Models\MsMaterialReception','destination_store_id');
    }

    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\MsMaterialReceptionDetail','destination_store_id');
    }
}
