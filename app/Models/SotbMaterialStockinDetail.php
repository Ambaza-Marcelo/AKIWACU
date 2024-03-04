<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialStockinDetail extends Model
{
    //
    protected $table = 'sotb_material_stockin_details';
    protected $fillable = [
        'date',
        'quantity',
        'unit',
        'price_nvat',
        'vat',
        'price_wvat',
        'total_amount_purchase',
        'total_amount_selling',
        'purchase_price',
        'selling_price',
        'stockin_no',
        'receptionist',
        'handingover',
        'stockin_signature',
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
        'material_id',
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

    public function material(){
        return $this->belongsTo('App\Models\SotbMaterial');
    }
}
