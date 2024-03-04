<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialStockin extends Model
{
    //
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
}
