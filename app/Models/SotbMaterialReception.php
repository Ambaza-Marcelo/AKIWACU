<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialReception extends Model
{
    //
    protected $table = 'sotb_material_receptions';
    protected $fillable = [
        'date',
        'reception_no',
        'reception_signature',
        'invoice_no',
        'order_no',
        'receptionist',
        'handingover',
        'vat_taxpayer',
        'vat_supplier_payer',
        'invoice_currency',
        'waybill',
        'description',
        'rejected_motif',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'supplier_id',
        'destination_bg_store_id',
        'destination_md_store_id',
        'destination_sm_store_id',
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\SotbSupplier');
    }

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
