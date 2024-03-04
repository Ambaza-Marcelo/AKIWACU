<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialTransfertDetail extends Model
{
    //
    protected $table = 'sotb_material_transfert_details';

    protected $fillable = [
        'date',
        'quantity_requisitioned',
        'quantity_transfered',
        'unit',
        'price',
        'transfer_no',
        'requisition_no',
        'transfer_signature',
        'description',
        'rejected_motif',
        'total_value_requisitioned',
        'total_value_transfered',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'material_id',
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

    public function material(){
        return $this->belongsTo('App\Models\SotbMaterial');
    }
}
