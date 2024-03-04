<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialExtraBigStoreDetail extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'store_signature',
        'emplacement',
        'manager',
        'quantity',
        'unit',
        'total_value',
        'threshold_quantity',
        'verified',
        'material_id',
        'created_by',
        'updated_by',
        'description',
    ];

    public function material(){
        return $this->belongsTo('App\Models\Material');
    }
}
