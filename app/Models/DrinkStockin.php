<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkStockin extends Model
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
        'destination_extra_store_id',
        'item_movement_type'
    ];

    public function drinkSmallStore(){
        return $this->belongsTo('App\Models\DrinkSmallStore');
    }

    public function drinkbigStore(){
        return $this->belongsTo('App\Models\DrinkBigStore');
    }

    public function drinkExtraStore(){
        return $this->belongsTo('App\Models\DrinkExtraStore');
    }
}
