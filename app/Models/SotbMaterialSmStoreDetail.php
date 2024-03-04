<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialSmStoreDetail extends Model
{
    //
    protected $table = 'sotb_material_sm_store_details';
    protected $fillable = [
        'name',
        'code',
        'store_signature',
        'emplacement',
        'manager',
        'quantity',
        'unit',
        'total_value',
        'cump',
        'purchase_price',
        'total_cump',
        'threshold_quantity',
        'verified',
        'material_id',
        'created_by',
        'updated_by',
        'description',
    ];

    public function material(){
        return $this->belongsTo('App\Models\SotbMaterial');
    }
}
