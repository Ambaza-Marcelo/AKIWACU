<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialStockinDetail extends Model
{
    //
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
        'destination_sm_store_id',
        'destination_bg_store_id',
        'destination_extra_store_id'
    ];

    public function destinationSmallStore(){
        return $this->belongsTo('App\Models\MaterialSmallStore');
    }

    public function destinationbigStore(){
        return $this->belongsTo('App\Models\MaterialBigStore');
    }

    public function destinationExtraStore(){
        return $this->belongsTo('App\Models\MaterialExtraStore');
    }

    public function material(){
        return $this->belongsTo('App\Models\Material');
    }
}
