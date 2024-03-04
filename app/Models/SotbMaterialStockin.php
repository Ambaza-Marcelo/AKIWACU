<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialStockin extends Model
{
    //
    protected $table = 'sotb_material_stockins';
    protected $fillable = [
        'date',
        'stockin_no',
        'stockin_signature',
        'receptionist',
        'handingover',
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
        'destination_bg_store_id',
        'destination_md_store_id',
        'destination_sm_store_id'
    ];

    public function destinationBgStore(){
        return $this->belongsTo('App\Models\SotbMaterialBgStore');
    }

    public function destinationMdStore(){
        return $this->belongsTo('App\Models\SotbMaterialMdStore');
    }

    public function destinationSmStore(){
        return $this->belongsTo('App\Models\SotbMaterialSmStore');
    }
}
