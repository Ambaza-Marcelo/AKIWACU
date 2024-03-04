<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialStoreDetail extends Model
{
    //
    protected $table = 'ms_material_store_details';
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
        return $this->belongsTo('App\Models\MsMaterial');
    }
}
