<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialStockout extends Model
{
    //
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
        'store_type',
        'origin_sm_store_id',
        'origin_bg_store_id',
        'origin_extra_store_id',
        'status'
    ];

    public function originSmallStore(){
        return $this->belongsTo('App\Models\MaterialSmallStore');
    }

    public function originBigStore(){
        return $this->belongsTo('App\Models\MaterialBigStore');
    }

    public function originExtraStore(){
        return $this->belongsTo('App\Models\MaterialExtraBigStore');
    }
}
