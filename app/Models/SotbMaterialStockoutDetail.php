<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialStockoutDetail extends Model
{
    //
    protected $table = 'sotb_material_stockout_details';
    protected $fillable = [
        'material_id',
        'date',
        'quantity',
        'unit',
        'price',
        'stockout_no',
        'stockout_signature',
        'requisition_no',
        'description',
        'rejected_motif',
        'total_value',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'asker',
        'destination',
        'status',
        'store_type',
        'origin_bg_store_id',
        'origin_md_store_id',
        'origin_sm_store_id',
    ];

    public function originBgStore(){
        return $this->belongsTo('App\Models\SotbMaterialBgStore');
    }

    public function originMdStore(){
        return $this->belongsTo('App\Models\SotbMaterialMdStore');
    }

    public function originSmStore(){
        return $this->belongsTo('App\Models\SotbMaterialSmStore');
    }

    public function material(){
        return $this->belongsTo('App\Models\SotbMaterial');
    }
}
