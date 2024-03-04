<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialTransfert extends Model
{
    //
    protected $table = 'sotb_material_transferts';

    protected $fillable = [
        'date',
        'transfer_no',
        'requisition_no',
        'transfer_signature',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'origin_bg_store_id',
        'origin_md_store_id',
        'destination_md_store_id',
        'destination_sm_store_id',
    ];

    public function originBgStore(){
        return $this->belongsTo('App\Models\SotbMaterialBgStore');
    }
    public function destinationMdStore(){
        return $this->belongsTo('App\Models\SotbMaterialMdStore');
    }

    public function originMdStore(){
        return $this->belongsTo('App\Models\SotbMaterialMdStore');
    }
    public function destinationSmStore(){
        return $this->belongsTo('App\Models\SotbMaterialSmStore');
    }
}
