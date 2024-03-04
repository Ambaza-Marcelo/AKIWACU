<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialStockout extends Model
{
    //
    protected $table = 'sotb_material_stockouts';
    protected $fillable = [
        'date',
        'stockout_no',
        'stockout_signature',
        'requisition_no',
        'asker',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
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
}
